<?php

namespace App\Controller;

use App\Entity\VirementsEcash;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CompteEcashRepository;
use App\Repository\EntreprisesRepository;
use App\Repository\ParticuliersRepository;
use Symfony\Component\Validator\Validation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Encoder\JsonEncoder as EncoderJsonEncoder;

class VirementEcashController extends AbstractController
{
    #[Route('/virement/ecash', name: 'app_virement_ecash')]
    public function index(): Response
    {
        return $this->render('virement_ecash/index.html.twig', [
            'controller_name' => 'VirementEcashController',
        ]);
    }

    #[Route('/api/ecash_compte', name: 'get_ecash_compte')]
    public function ecash_compte(Request $request, SerializerInterface $serializer, ParticuliersRepository $particuliersRepository, EntreprisesRepository $entreprisesRepository): Response
    {

        $id=$request->query->get("id");
        $typeId=$request->query->get("typeId");
        $compte_ecash=null;
        if($typeId==6){
            $compte_ecash = $entreprisesRepository->find($id)->getCompteEcash();
        }else{
            $compte_ecash = $particuliersRepository->find($id)->getCompteEcash();
        }

        // dd($entreprise,$particulier);
        $data = $serializer->serialize([
            "compte_ecash" => $compte_ecash,
        ], EncoderJsonEncoder::FORMAT);

        return new JsonResponse(
            [
                'data' => json_decode($data),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
    }

    #[Route('/api/number/compte', name: 'ecash_compte_number')]
    public function ecash_compte_number(Request $request, SerializerInterface $serializer, ParticuliersRepository $particuliersRepository, EntreprisesRepository $entreprisesRepository , UserRepository $userRepository): Response
    {

        $number=$request->query->get("number");
        $user=$userRepository->findOneBy(["numero"=>$number]);
        $typeId=$user->getType()->getId();
        
        $compte_ecash=null;
        if($typeId==6){
            $compte_ecash = $entreprisesRepository->findOneBy(["num_tel"=>$number])->getCompteEcash();
        }else{
            $compte_ecash = $particuliersRepository->findOneBy(["num_tel"=>$number])->getCompteEcash();
        }

        $data = $serializer->serialize([
            "compte_ecash" => $compte_ecash->getNumeroCompte(),
        ], EncoderJsonEncoder::FORMAT);

        return new JsonResponse(
            [
                'data' => json_decode($data),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
    }

    // ecash_compte_number
    #[Route('/api/virement/ecash', name: 'api_virement_ecash', methods: ['POST'])]
    public function virement_ecash(Request $request, CompteEcashRepository $compteEcashRepository ,EntityManagerInterface $entityManager,SerializerInterface $serializer): Response
    {

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

        $constraint = [
            "numero_compte_envoyeur" => [
                new Assert\NotBlank(),
                new Assert\Length(["min" => 6])
            ],
            "numero_compte_receveur" => [
                new Assert\NotBlank(),
                new Assert\Length(["min" =>6])
            ],
            "montant" => [
                new Assert\NotBlank(),
                new Assert\Type(["type"=>"integer"])
            ],
        ];

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
        
        $EcashCompteEnvoyeurFound = $compteEcashRepository->findOneBy(["numeroCompte"=>$data->numero_compte_envoyeur]);
        $EcashCompteReceveurFound =  $compteEcashRepository->findOneBy(["numeroCompte"=>$data->numero_compte_receveur]);
        if($EcashCompteEnvoyeurFound->getSolde()>=(double)$data->montant){
            // decrediter le sous compte envoyeur
            $decrediteEcashCompteEnvoyer=$compteEcashRepository->find($EcashCompteEnvoyeurFound->getId());
            $decredite=$decrediteEcashCompteEnvoyer->getSolde()-(double)$data->montant;
            $decrediteEcashCompteEnvoyer->setSolde($decredite);
            $entityManager->persist($decrediteEcashCompteEnvoyer);

            // créditer le sous compte receveur
            $crediteEcashCompteReceveur=$compteEcashRepository->find($EcashCompteReceveurFound->getId());
            $credite=$crediteEcashCompteReceveur->getSolde()+(double)$data->montant;
            $crediteEcashCompteReceveur->setSolde($credite);
            $entityManager->persist($crediteEcashCompteReceveur);

            //store virement Ecash
            $virement_ecash=new VirementsEcash();
            $virement_ecash->setIdCompteEnvoyeur($EcashCompteEnvoyeurFound);
            $virement_ecash->setIdCompteReceveur($EcashCompteReceveurFound);
            $virement_ecash->setMontant((double)$data->montant);
            $virement_ecash->setDateTransaction(new \DateTime());
            $entityManager->persist($virement_ecash);
            
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
            // dd($decrediteSousCompteEnvoyer);
        }else{
            return new JsonResponse(
                [
                    "code"=> Response::HTTP_BAD_REQUEST,
                    'message' => "Recharger votre Compte le solde est insuffisant",
                ],
                Response::HTTP_BAD_REQUEST,
                array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
            );
        }

        $data = $serializer->serialize([
            "virementEcash" => $virement_ecash,
        ], EncoderJsonEncoder::FORMAT);

        return new JsonResponse(
            [
                'message' => "REGLEMENT_ECASH_CREATED",
                'data'=> json_decode($data),
            ],
            Response::HTTP_CREATED,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
    }
}
