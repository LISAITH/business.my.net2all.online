<?php

namespace App\Controller;

use App\Repository\CollaborationRepository;
use App\Repository\EnseignesRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder as EncoderJsonEncoder;

class CollaborationController extends AbstractController
{
    #[Route('/api/collaborations/{id_user}/{type_user}/{id_service}', name: 'get-collab')]
    public function collab($id_service, $id_user, $type_user, SerializerInterface $serializer, CollaborationRepository $collaRepo, EnseignesRepository $ensRepo): Response
    {

        $collaborations=[];
        $collaborations_enseignes=[];
        $collaborations_enseignes_id=[];
        if($type_user==1){
            $collaborations=$collaRepo->findBy(['service_id'=>$id_service, 'user_id'=>$id_user]);
            foreach ($collaborations as $collab) {
                array_push($collaborations_enseignes, $ensRepo->find($collab->getEnseigneId()));
            }
            foreach ($collaborations as $collab) {
                array_push($collaborations_enseignes_id, $ensRepo->find($collab->getEnseigneId())->getId());
            }
            
        }else if($type_user==6){
            // $collaboration_reçu=$collaRepo->findBy(['service_id'=>$id_service, 'destinataire_id'=>$id_user]);
        } 
    
        return new JsonResponse(
            [
                'collaborations' => $serializer->serialize($collaborations, EncoderJsonEncoder::FORMAT), 
                'enseignes'=>$serializer->serialize($collaborations_enseignes, EncoderJsonEncoder::FORMAT),
                'enseignes_id'=>$serializer->serialize($collaborations_enseignes_id, EncoderJsonEncoder::FORMAT)
            ],
            Response::HTTP_OK, 
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
        
    }
    
    #[Route('/api/membres/collaborations/{id_service}/{id_enseigne}', name: 'get-collab-member')]
    public function membres($id_service, $id_enseigne, SerializerInterface $serializer, CollaborationRepository $collaRepo, EnseignesRepository $ensRepo, UserRepository $userRepo): Response
    {

        $collaborations=[];
        $collaborations_users=[];
        $collaborations_particulier=[];

        $collaborations=$collaRepo->findBy(['service_id'=>$id_service, 'enseigne_id'=>$id_enseigne]);
        foreach ($collaborations as $collab) {
            // $collaborations_particulier += array($id=>$part);
            array_push($collaborations_users, $userRepo->find($collab->getUserId()));
            array_push($collaborations_particulier, $userRepo->find($collab->getUserId())->getParticuliers()[0]);
        }
    
        return new JsonResponse(
            [
                'collaborations' => $serializer->serialize($collaborations, EncoderJsonEncoder::FORMAT), 
                'users'=>$serializer->serialize($collaborations_users, EncoderJsonEncoder::FORMAT),
                'particuliers'=>$serializer->serialize($collaborations_particulier, EncoderJsonEncoder::FORMAT),
            ],
            Response::HTTP_OK, 
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
        
    }
}
