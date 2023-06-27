<?php

namespace App\Controller;

use App\Entity\CompteEcash;
use App\Entity\Entreprises;
use App\Entity\Particuliers;
use App\Entity\Recommandations;
use App\Entity\SousCompte;
use App\Entity\User;
use App\Repository\ConfigMessagesRepository;
use App\Repository\PaysRepository;
use App\Repository\RecommandationsRepository;
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
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Encoder\JsonEncoder as EncoderJsonEncoder;

class RecommandationController extends AbstractController
{

    private $apiRegisterUrl="users/register";

    #[Route(path: '/api/recommandations/new', name: 'recommandation', methods: ['POST'])]
    public function index(Request $request, PaysRepository $paysRepository, ServicesRepository $servicesRepository,ConfigMessagesRepository $configMessagesRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, TypeRepository $typeRepository, SerializerInterface $serializer, RecommandationsRepository $recommandationsRepository, UserRepository $userRepository): Response
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

        /// Array that represent a list of constraint
        $constraint = [
            'user_id' => [
                new Assert\NotBlank(),
            ],
            'pays_id' => [
                new Assert\NotBlank(),
            ],
            'user_type' => [
                new Assert\NotBlank(),
            ],
            'numero_recommande' => [
                new Assert\Optional(),
            ],
            'email_recommande' => [
                new Assert\Optional(),
            ]
        ];
        $error='';
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

        if ($data->numero_recommande  == null && $constraint["email_recommande"] ==null) {
            return new JsonResponse( 
                [
                    'error' => "INVALID_REQUEST_DATA1",
                ],
                Response::HTTP_BAD_REQUEST,
                array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
            );
        }

        if ($data->numero_recommande !=null) {
            $password= $this->getRandomText(6);

            $user = new User();
            $type=$typeRepository->find($data->user_type);
            $user->setType($type);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $password
                )
            );
            $user->setEmail(str_replace("+","",$data->numero_recommande).'@net2all.net');
            $user->setNumero($data->numero_recommande);
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

            if($data->user_type==1){
                /// Create new particular
                $particular = new Particuliers();
                $particular->setNom("Net2All");
                $particular->setPrenoms("Invité");
                $particular->setNumTel($data->numero_recommande);
                $particular->setUser($user);
                $particular->setPays($pays);

                // Set [CompteEcash]
                $ecash->setParticulier($particular);

                /// Save particular to database
                $entityManager->persist($particular);
            }else if($data->user_type==6){
                /// Create new Enterprise
                $enterprise = new Entreprises();
                $enterprise->setUser($user);
                $enterprise->setNom("Net2All");
                $enterprise->setPrenoms("Invité");
                $enterprise->setNumTel($data->numero_recommande);
                $enterprise->setPays($pays);
                $enterprise->setNomEntreprise("Entreprise invitée");

                // Set [CompteEcash]
                $ecash->setEntreprise($enterprise);

                /// Save enterprise to database
                $entityManager->persist($enterprise);
            }

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
            }catch (Exception $e){
                return new JsonResponse(
                    [
                        "code"=> Response::HTTP_NOT_FOUND,
                        'message' => "CANNOT_CREATE_USER",
                    ],
                    Response::HTTP_NOT_FOUND,
                    array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
                );
            }
        }

        $recommadation= new Recommandations();
        $recommadation->setNumeroRecommande(''.$data->numero_recommande);
        $recommadation->setUserId((int)$data->user_id);
        $recommadation->setUserType((int)$data->user_type);
        $recommadation->setPaysId((int)$data->pays_id);
        $recommadation->setGuestId($user->getId());
        $recommadation->setGuestTempPassword($password);
        $uniqueId= uniqid();
        $recommadation->setLink($uniqueId);
        $entityManager->persist($recommadation);
        $entityManager->flush();


        $title = $configMessagesRepository->findOneBy(['title'=>'Recommandation Title'])? $configMessagesRepository->findOneBy(['title'=>'Recom Title'])->getTitle() : "Business360";

        //$msg = "Bienvenu+a+NET2ALL.+Connecter+vous+à+votre+panel+unique+à+adresse+suivante+:+https://dev.n2a.online/business/public/index.php/login.+Login:+($email)+mdp:+($email)";
        $phone=str_replace("+","",$data->numero_recommande);
        $send_user=$userRepository->find((int)$data->user_id);
        $full_name="";
        if($send_user->getEmail()!=null){
            if($send_user->getEntreprises()!=null){
                $full_name= $send_user->getEntreprises()[0]->getPrenoms().' '.strtoupper($send_user->getEntreprises()[0]->getNom());
            }else{
                $full_name= $send_user->getParticuliers()[0]->getPrenoms().' '.strtoupper($send_user->getParticuliers()[0]->getNom());
            }
        }
        $full_name=str_replace(' ', '+', $full_name);
        $premsg=$configMessagesRepository->findOneBy(['title'=>'Recommandation message'])? $configMessagesRepository->findOneBy(['title'=>'Recommandation message']): "Business360";
        $msg = "$full_name+vous+invite+sur+Business360.+Accedez+au+panel++via+le+lien+suivant+https://my.net2all.net/public/accept/recommandation/$uniqueId.+Identifiant:+$phone+Mot_de_passe:+$password";
        // $msg = wordwrap($msg, 70);
        $sms = file_get_contents("http://130.185.251.88/api/http/sendmsg.php?user=magmatel2&password=SMSmagamatelys2@2017&from=$title&to=%2B$phone&text=$msg&api=14265");
        return new JsonResponse(
            [
                'message' => "SUCCESS"
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
    }

    #[Route('/api/recommandations/user/{id}', name: 'recommandation-get')]
    public function index2($id, SerializerInterface $serializer, RecommandationsRepository $recommandationsRepository): Response
    {

        return new JsonResponse(
            [
                'data' => json_decode($serializer->serialize($recommandationsRepository->findBy(["user_id"=>$id]), EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
    }

    #[Route('/api/accept/recommandation/{id}', name: 'recommandation-accept')]
    public function accept($id, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, RecommandationsRepository $recommandationsRepository): Response
    {
        $rec=$recommandationsRepository->findOneBy(['link'=>$id]);
        $response=404;
        $email="";
        $password="";
        if($rec){
            if(!$rec->getStatus()?? false){
                $rec->setStatus(1);
                $entityManagerInterface->persist($rec);
                $entityManagerInterface->flush();
                $response=200;
                if($rec->getEmailRecommande()!=null){
                    $email=$rec->getEmailRecommande();
                }else if($rec->getNumeroRecommande()!=null){
                    $email=str_replace("+","", $rec->getNumeroRecommande()).'@net2all.net';
                }
                $password=$rec->getGuestTempPassword();
            }else{
                $response=201;
            }
        }

        return new JsonResponse(
            [
                'data' =>$response,
                'login'=> json_decode($serializer->serialize(['email'=>$email, "password"=>$password], EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK,
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