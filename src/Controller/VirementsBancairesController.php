<?php

namespace App\Controller;

use App\Entity\VirementsBancaires;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CompteEcashRepository;
use Symfony\Component\Validator\Validation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Encoder\JsonEncoder as EncoderJsonEncoder;

class VirementsBancairesController extends AbstractController
{
    #[Route('/virements/bancaires', name: 'app_virements_bancaires')]
    public function index(): Response
    {
        return $this->render('virements_bancaires/index.html.twig', [
            'controller_name' => 'VirementsBancairesController',
        ]);
    }

    #[Route('/api/virement/bancaire', name: 'api_virement_bancaire', methods: ['POST'])]
    public function virement_bancaire(Request $request, CompteEcashRepository $compteEcashRepository ,EntityManagerInterface $entityManager,SerializerInterface $serializer): Response
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
            "numero_bank_receveur" => [
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

        $CompteEnvoyeurFound = $compteEcashRepository->findOneBy(["numeroCompte"=>$data->numero_compte_envoyeur]);
        if($CompteEnvoyeurFound->getSolde()>=(double)$data->montant){
            // decrediter le sous compte envoyeur
            $decrediteCompteEnvoyer=$compteEcashRepository->find($CompteEnvoyeurFound->getId());
            $decredite=$decrediteCompteEnvoyer->getSolde()-(double)$data->montant;
            $decrediteCompteEnvoyer->setSolde($decredite);
            $entityManager->persist($decrediteCompteEnvoyer);

            // créditer le sous compte receveur
            // $crediteEcashCompteReceveur=$compteEcashRepository->find($EcashCompteReceveurFound->getId());
            // $credite=$crediteEcashCompteReceveur->getSolde()+(double)$data->montant;
            // $crediteEcashCompteReceveur->setSolde($credite);
            // $entityManager->persist($crediteEcashCompteReceveur);

            //store virement Ecash
            $virement_bancaire=new VirementsBancaires();
            $virement_bancaire->setIdCompteEcash($CompteEnvoyeurFound);
            $virement_bancaire->setNumeroBancaire($data->numero_bank_receveur);
            $virement_bancaire->setMontant((double)$data->montant);
            $virement_bancaire->setDateTransaction(new \DateTime());
            $entityManager->persist($virement_bancaire);
            
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
            "virement_bancaire" => $virement_bancaire,
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
