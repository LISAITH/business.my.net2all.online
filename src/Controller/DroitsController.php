<?php

namespace App\Controller;

use App\Repository\DroitsRepository;
use App\Repository\ProfilesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder as EncoderJsonEncoder;

class DroitsController extends AbstractController
{
    #[Route('/api/droits/services/{id_service}', name: 'get-droits')]
    public function membres($id_service, SerializerInterface $serializer, DroitsRepository $droitsRepo): Response
    {

        $droits=$droitsRepo->findBy(['service_id'=>$id_service]);
    
        return new JsonResponse(
            [
                'data' => $serializer->serialize($droits, EncoderJsonEncoder::FORMAT)
            ],
            Response::HTTP_OK, 
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
        
    }
}
