<?php

namespace App\Controller;

use App\Repository\CollaborationRequestRepository;
use App\Repository\FacturesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder as EncoderJsonEncoder;

class FacturesController extends AbstractController
{
    #[Route('/api/particulier/factures/{id}', name: 'get-factures')]
    public function type($id, SerializerInterface $serializer, FacturesRepository $facRepo): Response
    {
        return new JsonResponse(
            [
                'data' => json_decode($serializer->serialize($facRepo->findBy(['particulier_id'=>$id]), EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK, 
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
        
    }
}
