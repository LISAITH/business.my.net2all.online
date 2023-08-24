<?php

namespace App\Controller\Api;

use App\Repository\ApiServicesRepository;
use App\Repository\EnseignesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/cashline', name: 'api_cashline_')]
class ApiCashlineController extends AbstractController
{
    private ApiServicesRepository $apiServicesRepository;
    private EnseignesRepository $enseignesRepository;

    public function __construct(ApiServicesRepository $apiServicesRepository, EnseignesRepository $enseignesRepository)
    {
        $this->apiServicesRepository = $apiServicesRepository;
        $this->enseignesRepository = $enseignesRepository;
    }

    #[Route('/enseignes', name: 'enseignes')]
    public function enseignes()
    {
        $apiServices = $this->apiServicesRepository->findBy(['id_services' => 2]);

        $enseignes = [];

        foreach ($apiServices as $apiService) {
            $enseigne = $this->enseignesRepository->findOneBy(['id' => $apiService->getIdEnseigne()]);

            $enseignes[] = [
                'nom' => $enseigne->getNomEnseigne(),
                'logo' => $enseigne->getUrlImage(),
                'entreprise' => $enseigne->getEntreprise()->getNomEntreprise(),
                'lien' => $_ENV['cashlineLink'] . strtolower(str_replace(' ', '', $enseigne->getNomEnseigne())) . '/public'
            ];
        }

        return new JsonResponse($enseignes);
    }

}
