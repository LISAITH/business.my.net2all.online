<?php

namespace App\Controller;

use App\Repository\CollaborationRepository;
use App\Repository\CollaborationRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder as EncoderJsonEncoder;

class CollaborationRequestController extends AbstractController
{
    #[Route('/api/collaboration_requests/{id_user}/{type_user}/{id_service}', name: 'get-collab-req')]
    public function type($id_service, $id_user, $type_user, SerializerInterface $serializer, CollaborationRequestRepository $collaRepo): Response
    {

        $collaboration_reçu=[];
        $collaboration_envoye=[];
        if($type_user==1){
            $collaboration_reçu=$collaRepo->findBy(['service_id'=>$id_service, 'destinataire_type'=>"Particulier", 'destinataire_id'=>$id_user]);
            $collaboration_envoye=$collaRepo->findBy(['service_id'=>$id_service,'expediteur_type'=>"Particulier", 'expediteur_id'=>$id_user]);
        }else if($type_user==6){
            $collaboration_reçu=$collaRepo->findBy(['service_id'=>$id_service, 'destinataire_id'=>$id_user]);
            $collaboration_envoye=$collaRepo->findBy(['service_id'=>$id_service,'expediteur_type'=>"Entreprise", 'expediteur_id'=>$id_user]);
        } 
    
        return new JsonResponse(
            [
                'sendBy' => $serializer->serialize($collaboration_envoye, EncoderJsonEncoder::FORMAT), 
                'getBy'=>$serializer->serialize($collaboration_reçu, EncoderJsonEncoder::FORMAT)
            ],
            Response::HTTP_OK, 
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
        
    }
}
