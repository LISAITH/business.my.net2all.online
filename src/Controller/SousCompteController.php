<?php

namespace App\Controller;

use App\Repository\SousCompteRepository;
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

class SousCompteController extends AbstractController
{
    #[Route('/sous/compte', name: 'app_sous_compte')]
    public function index(): Response
    {
        return $this->render('sous_compte/index.html.twig', [
            'controller_name' => 'SousCompteController',
        ]);
    }

    #[Route('/api/virement_svp', name: 'api_virement_svp', methods: ['POST'])]
    public function virement_svp(Request $request, SousCompteRepository $souscompteRepository ,CompteEcashRepository $compteEcashRepository ,EntityManagerInterface $entityManager,SerializerInterface $serializer,ParticuliersRepository $particuliersRepository, EntreprisesRepository $entreprisesRepository): Response
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
            "idType" => [
                new Assert\Optional(),
            ],
            "idUser" => [
                new Assert\Optional(),
            ],
            "numero_sous_compte" => [
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

            $id=$data->idUser;
            $typeId=$data->idType;
            $compte_ecash=null;
            if($typeId==6){
                $compte_ecash = $entreprisesRepository->find($id)->getCompteEcash();
            }else{
                $compte_ecash = $particuliersRepository->find($id)->getCompteEcash();
            }

            $EcashCompteFound = $compteEcashRepository->findOneBy(["numeroCompte"=>$compte_ecash->getNumeroCompte()]);
            $sousCompteFound = $souscompteRepository->findOneBy(["numeroSousCompte"=>$data->numero_sous_compte]);

            // créditer le sous compte receveur
            $crediteEcashCompte=$compteEcashRepository->find($EcashCompteFound->getId());
            $credite=$crediteEcashCompte->getSolde()+(double)$data->montant;
            $crediteEcashCompte->setSolde($credite);
            $entityManager->persist($crediteEcashCompte);

            // decrediter le sous compte envoyeur
            $decrediteSousCompte=$souscompteRepository->find($sousCompteFound->getId());
            $decredite=$decrediteSousCompte->getSolde()-(double)$data->montant;
            $decrediteSousCompte->setSolde($decredite);
            $entityManager->persist($decrediteSousCompte);

            
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
            "compte_ecash" => $crediteEcashCompte,
            "sous_compte" => $decrediteSousCompte,
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
