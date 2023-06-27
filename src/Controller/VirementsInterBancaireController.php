<?php

namespace App\Controller;

use App\Entity\VirementsInterBancaire;
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

class VirementsInterBancaireController extends AbstractController
{
    #[Route('/virements/inter/bancaire', name: 'app_virements_inter_bancaire')]
    public function index(): Response
    {
        return $this->render('virements_inter_bancaire/index.html.twig', [
            'controller_name' => 'VirementsInterBancaireController',
        ]);
    }

    #[Route('/api/virement/inter_bancaire', name: 'api_virement_inter_bancaire', methods: ['POST'])]
    public function virement_inter_bancaire(Request $request,EntityManagerInterface $entityManager,SerializerInterface $serializer): Response
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
            "numero_bank_envoyeur" => [
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

            //store virement Ecash
        $virement_inter_bancaire=new VirementsInterBancaire();
        $virement_inter_bancaire->setNumeroBankEnvoyeur($data->numero_bank_envoyeur);
        $virement_inter_bancaire->setNumeroBankReceveur($data->numero_bank_receveur);
        $virement_inter_bancaire->setMontant((double)$data->montant);
        $virement_inter_bancaire->setDateTransaction(new \DateTime());
        $entityManager->persist($virement_inter_bancaire);
            
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

        $data = $serializer->serialize([
            "virement_inter_bancaire" => $virement_inter_bancaire,
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
