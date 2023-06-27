<?php

namespace App\Controller\Api;

use App\Entity\CompteEcash;
use App\Entity\Entreprises;
use App\Entity\Particuliers;
use App\Entity\SousCompte;
use App\Entity\User;
use App\Repository\PaysRepository;
use App\Repository\ServicesRepository;
use App\Repository\TypeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder as EncoderJsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class ApiRegistrationController extends AbstractController
{
    #[Route(path: '/api/users/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SerializerInterface $serializer, ServicesRepository $servicesRepository, TypeRepository $typeRepository, PaysRepository $paysRepository, UserRepository $userRepository): Response
    {
		
        /// Create an new validator
        $validator = Validation::createValidator();

        $data = json_decode($request->getContent());

        if ($data == null) {
            return new JsonResponse( 
                [
                    'error' => "INVALID_REQUEST_DATA",
                ],
                Response::HTTP_BAD_REQUEST,
                array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
            );
        }

        $type = null;
        if (in_array($data->type, [1, 6])) {
            $type = $typeRepository->find($data->type);
        }

        /// Array that represent a list of constraint
        $constraint = [
            'password' => [
                new Assert\NotBlank(),
                new Assert\Length(["min" => 6])
            ],
            'email' => [
                new Assert\NotBlank(),
                new Assert\Email(),
            ],
            "type" => [
                new Assert\NotBlank(),
                new Assert\Choice([1, 6])
            ],
            "nom" => [
                new Assert\NotBlank(),
                new Assert\Length(["min" => 1])
            ],
            "prenoms" => [
                new Assert\NotBlank(),
                new Assert\Length(["min" => 1])
            ],
            "genre" => [
                new Assert\Optional(),
            ],
            "num_tel" => [
                new Assert\Optional(),
            ],
            "nom_entreprise" => [
                new Assert\Optional(),
            ],
            "url_image" => [
                new Assert\Optional(),
            ],
            "pays_id" => [
                new Assert\NotBlank(),
                new Assert\Choice([1,2,3,4,5,6,7,8])
            ],
        ];

        /// Check if [Request] type exist in database
        if ($type != null) {
            if ($type->getId() == 1) {
                $constraint["genre"] = [
                    new Assert\NotBlank(),
                    new Assert\Choice(["m", "f"])
                ];
            } else if ($type->getId() == 6) {
                $constraint["nom_entreprise"] = [
                    new Assert\NotBlank(),
                    new Assert\Length(["min" => 1])
                ];
            }
        }

        // Create [Assert\Collection] to validate request data
        $constraints = new Assert\Collection($constraint);

        /// Process validation
        $errors = $validator->validate(json_decode($request->getContent(), true), $constraints);

        /// If not validated return [Bad Request] response with detected errors
        if ($errors->count()) {
            return new JsonResponse(
                [
                    "code"=> Response::HTTP_BAD_REQUEST,
                    'error' => $serializer->serialize($errors, "json"),
                ],
                Response::HTTP_BAD_REQUEST,
                array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
            );
        }

        /// Check if exist user with Request email
        $existedUser = $userRepository->findBy(["email"=>$data->email]);

        if($existedUser!=null){
            return new JsonResponse(
                [
                    "code"=> Response::HTTP_BAD_REQUEST,
                    'message' => "EMAIL_ALREADY_EXIST",
                ],
                Response::HTTP_BAD_REQUEST,
                array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
            );
        }

        /// Check if exist user with Request phone number
        $existedUser = $userRepository->findBy(["numero"=>$data->num_tel]);
        if($existedUser!=null){
            return new JsonResponse(
                [
                    "code"=> Response::HTTP_BAD_REQUEST,
                    'message' => "PHONE_NUMBER_ALREADY_EXIST",
                ],
                Response::HTTP_BAD_REQUEST,
                array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
            );
        }

        /// Create in user
        $user = new User();
        $user->setType($type);
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $data->password
            )
        );
        $user->setEmail($data->email);
        $user->setNumero($data->num_tel);
        $user->setStatus(true);

        /// Save user to database
        $entityManager->persist($user);

        //Get country of user
        $pays = $paysRepository->find($data->pays_id);

        /// Generate new accountNumber for user's CompteEcash
        $accountNumber = $this->getRandomText(10);

        /// Create user's CompteEcash
        $ecash = new CompteEcash();
        $ecash->setNumeroCompte($accountNumber);
        $ecash->setSolde(0);

        /// Check if registration request is for Enterprise account or Particular account
        if ($type->getId() == 1) /*Particular account*/{
            /// Create new particular
            $particular = new Particuliers();
            $particular->setNom($data->nom);
            $particular->setPrenoms($data->prenoms);
            $particular->setNumTel($data->num_tel);
            $particular->setUser($user);
            $particular->setPays($pays);

            // Set [CompteEcash]
            $ecash->setParticulier($particular);

            /// Save particular to database
            $entityManager->persist($particular);

        } else if ($type->getId() == 6)/*Enterprise account*/ {
            /// Create new Enterprise
            $enterprise = new Entreprises();
            $enterprise->setUser($user);
            $enterprise->setNumTel($data->num_tel);
            $enterprise->setNom($data->nom);
            $enterprise->setPrenoms($data->prenoms);
            $enterprise->setNomEntreprise($data->nom_entreprise);
            $enterprise->setUrlImage($data->url_image??"");
            $enterprise->setPays($pays);

            // Set [CompteEcash]
            $ecash->setEntreprise($enterprise);

            /// Save enterprise to database
            $entityManager->persist($enterprise);
        }

        $entityManager->persist($ecash);

        /// Get all services
        $services = $servicesRepository->findAll();

        /// Create [SousCompte] for each service
        foreach ($services as $service){
            $sousCompte = new SousCompte();
            $sousCompte->setSolde(0);
            $sousCompte->setCompteEcash($ecash);
            $sousCompte->setService($service);
            $suffix = substr($service->getLibelle(),0,4);
            $sousCompte->setNumeroSousCompte($accountNumber . "-" .$suffix);

            /// Save SousCompte to database
            $entityManager->persist($sousCompte);
        }
        try {
            $entityManager->flush();
        }catch (Exception){
            return new JsonResponse(
                [
                    "code"=> Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => "ERROR",
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
            );
        }
 
        $res = $serializer->serialize(
            [
                "user"=>$user,
                "particulier"=>$particular??null,
                "entreprise"=>$enterprise??null,
                "ecash"=>$ecash
            ],
            EncoderJsonEncoder::FORMAT,
        );
        return new JsonResponse(
            [
                'message' => "RESOURCE_CREATED",
                'data'=> json_decode($res),
            ],
            Response::HTTP_CREATED,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
    }

    protected function getRandomText($n): string
    {
        $characters = '0123456789ABCDEFGHILKMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}
