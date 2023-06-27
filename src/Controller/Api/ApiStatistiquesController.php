<?php

namespace App\Controller\Api;

use App\Entity\ApiCommunautes;
use App\Repository\ApiCommunautesRepository;
use App\Repository\ApiServicesRepository;
use App\Repository\CompteEcashRepository;
use App\Repository\EnseignesRepository;
use App\Repository\EntreprisesRepository;
use App\Repository\ParticuliersRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ApiStatistiquesController extends AbstractController
{
    #[Route(path: '/api/statistiques', name: 'api_statistiques', methods: ['GET'])]
    public function statistiques(Request $request, UserRepository $userRepository, ParticuliersRepository $particuliersRepository, EntreprisesRepository $entreprisesRepository, CompteEcashRepository $ecashRepository, EnseignesRepository $enseignesRepository): JsonResponse
    {
        $user_id = json_decode($request->getContent())->user_id??$request->get("user_id");
        if($user_id == null){
            return new JsonResponse(
                [
                    'error' => "HTTP_BAD_REQUEST",
                ],
                Response::HTTP_BAD_REQUEST,
                array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
            );
        }
        /**
         * Get  [Particuliers], [Entreprises] and [Enseignes], size
         */
        $particuliers = $particuliersRepository->count([]);
        $entreprises = $entreprisesRepository->count([]);
        $enseignes = $enseignesRepository->count([]);

        $user = $userRepository->find($user_id);
        $entreprise = $entreprisesRepository->findOneBy(["user" => $user]);
        $particulier = $particuliersRepository->findOneBy(["user" => $user]);
        $ecash = $ecashRepository->findOneBy(["entreprise" => $entreprise, "particulier" => $particulier]);

        $solde = $ecash?$ecash->getSolde():0;

        return new JsonResponse([
            'data' => [
                "particuliers" => $particuliers,
                "entreprises" => $entreprises,
                "enseignes" => $enseignes,
                "solde" => $solde
            ],
        ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
    }

    #[Route(path: '/api/statistiques/services/{service_id}', name: 'api_service_statistiques', methods: ['GET'])]
    public function serviceStatistiques($service_id, Request $request, EnseignesRepository $enseignesRepository, ApiServicesRepository $apiServicesRepository): JsonResponse
    {
        $totalEnseignes = $enseignesRepository->count([]);
        $totalAffiliated = $apiServicesRepository->count(['id_services'=>$service_id]);
        return new JsonResponse([
            'data' => [
                "totalEnseignes" => $totalEnseignes,
                "totalAffiliated" => $totalAffiliated,
            ],
        ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
    }

    #[Route(path: '/api/statistiques/communautes/{communaute_id}', name: 'api_communaute_statistiques', methods: ['GET'])]
    public function communautesStatistiques($communaute_id, Request $request, EnseignesRepository $enseignesRepository, ApiCommunautesRepository $apiCommunautesRepository): JsonResponse
    {
        $totalEnseignes = $enseignesRepository->count([]);
        $totalAffiliated = $apiCommunautesRepository->count(['id_communautes'=>$communaute_id]);
        return new JsonResponse([
            'data' => [
                "totalEnseignes" => $totalEnseignes,
                "totalAffiliated" => $totalAffiliated,
            ],
        ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
    }

}