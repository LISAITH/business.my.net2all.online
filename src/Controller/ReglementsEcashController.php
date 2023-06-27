<?php

namespace App\Controller;

use App\Entity\ReglementsEcash;
use App\Repository\SousCompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Encoder\JsonEncoder as EncoderJsonEncoder;

class ReglementsEcashController extends AbstractController
{
    #[Route('/reglements/ecash', name: 'app_reglements_ecash')]
    public function index(): Response
    {
        return $this->render('reglements_ecash/index.html.twig', [
            'controller_name' => 'ReglementsEcashController',
        ]);
    }

    #[Route('/api/reglement/ecash', name: 'api_reglement_ecash', methods: ['POST'])]
    public function reglement_ecash(Request $request, SousCompteRepository $souscompteRepository ,EntityManagerInterface $entityManager,SerializerInterface $serializer): Response
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
            "numero_sous_compte_envoyeur" => [
                new Assert\NotBlank(),
                new Assert\Length(["min" => 6])
            ],
            "numero_sous_compte_receveur" => [
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

        $sousCompteEnvoyeurFound = $souscompteRepository->findOneBy(["numeroSousCompte"=>$data->numero_sous_compte_envoyeur]);
        $sousCompteReceveurFound =  $souscompteRepository->findOneBy(["numeroSousCompte"=>$data->numero_sous_compte_receveur]);
        if($sousCompteEnvoyeurFound->getSolde()>=(double)$data->montant){
            // decrediter le sous compte envoyeur
            $decrediteSousCompteEnvoyer=$souscompteRepository->find($sousCompteEnvoyeurFound->getId());
            $decredite=$decrediteSousCompteEnvoyer->getSolde()-(double)$data->montant;
            $decrediteSousCompteEnvoyer->setSolde($decredite);
            $entityManager->persist($decrediteSousCompteEnvoyer);

            // créditer le sous compte receveur
            $crediteSousCompteReceveur=$souscompteRepository->find($sousCompteReceveurFound->getId());
            $credite=$crediteSousCompteReceveur->getSolde()+(double)$data->montant;
            $crediteSousCompteReceveur->setSolde($credite);
            $entityManager->persist($crediteSousCompteReceveur);

            //store reglement
            $reglement_ecash=new ReglementsEcash();
            $reglement_ecash->setIdSousCompteEnvoyeur($sousCompteEnvoyeurFound);
            $reglement_ecash->setIdSousCompteReceveur($sousCompteReceveurFound);
            $reglement_ecash->setMontant((double)$data->montant);
            $reglement_ecash->setDateTransaction(new \DateTime());
            $entityManager->persist($reglement_ecash);
            
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
            'souscompteEnvoiyeur' => $decrediteSousCompteEnvoyer,
            "souscompteReceveur" => $crediteSousCompteReceveur,
            "reglementEcash" => $reglement_ecash,
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
