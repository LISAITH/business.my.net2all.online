<?php

namespace App\Controller;

use App\Entity\Type;
use App\Entity\User;
use App\Entity\PointVente;
use App\Entity\Distributeur;
use App\Form\PointVenteType;
use App\Controller\AuthController;
use App\Form\PointVenteUpdateType;
use App\Repository\AbonnementRepository;
use App\Repository\PointVenteRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Services\AppServices;

class PointVenteController extends AuthController
{
    protected $type=2;

    #[Route('/point_ventes', name: 'point_ventes.index')]
    public function index(Request $request,PointVenteRepository $pointVenteRepository): Response
    {   
        // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();
        

        $point_ventes = $pointVenteRepository->findByDistributeurField($this->getUser()->getDistributeur()->getId());
      
        return $this->render('point_vente/index.html.twig', [
            'controller_name' => 'Business | Tous mes points de ventes',
            "point_ventes"=> $point_ventes
        ]);
    }



    #[Route('/point_ventes/create', name: 'point_ventes.create')]
    public function create(Request $request,ManagerRegistry $doctrine,UserPasswordHasherInterface $userPasswordHasher, AppServices $appServices, HttpClientInterface $httpClient): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();
        
       
        $type = $doctrine->getRepository(Type::class)->find(5);
        $distributeur = $this->getUser()->getDistributeur();
        $user = new User();
        $user->setType($type)->setStatus(true);
        $point_vente = new PointVente();
        $point_vente->setUser($user)->setStatus(true);
        $point_vente->setDistributeur($distributeur);

        $form = $this->createForm(PointVenteType::class, $point_vente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $point_vente->getUser()->setPassword($userPasswordHasher->hashPassword($user,$user->getPassword()));
            $entityManager = $doctrine->getManager();
            $entityManager->persist($point_vente);
            $entityManager ->flush();

            $url = $appServices->getBpayServerAddress() . '/create/compte/Bpay/' . $point_vente->getId() . '/' . $type->getId();
			$response = $httpClient->request('POST', $url, [
                'headers' => [
                    'Content-Type: application/json',
                    'Accept' => 'application/json',
                ]
            ]);

            $this -> addFlash("point_vente_add","le point vente  a été créé avec succès");
           
            return $this->redirectToRoute("point_ventes.index");
        }

     
        
        return $this->render('point_vente/create.html.twig', [
            'controller_name' => 'Business | Ajout de point de vente',
            'form' => $form->createView()
        ]);


    }



    #[Route('/point_ventes/status/{id}/unvalidate', name: 'point_ventes.status.unvalidate')]
    public function status_unvalidate(ManagerRegistry $doctrine, PointVenteRepository $pointVenteRepository ,  $id): Response

    {   
           // verifier si l'utilisateur est un partenaire
           if(!$this->checkAuthType()) return $this->forbidden();

        $entityManager = $doctrine->getManager();
        $point_vente = $pointVenteRepository->findOneByDistributeurField($id , $this->getUser()->getDistributeur()->getId());
        $point_vente ->setStatus(false);
        $entityManager->flush();
        $this -> addFlash("point_vente_unvalidate","le point vente \"".$point_vente->getNomPointVente(). "\" a été désactivé avec succès");

        return $this->redirectToRoute("point_ventes.index");
    }



    #[Route('/point_ventes/status/{id}validate', name: 'point_ventes.status.validate')]
    public function status_validate(ManagerRegistry $doctrine,PointVenteRepository $pointVenteRepository,  $id): Response

    {  // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $entityManager = $doctrine->getManager();
        $point_vente =  $pointVenteRepository->findOneByDistributeurField($id , $this->getUser()->getDistributeur()->getId());
        $point_vente->setStatus(true);
        $entityManager->flush();
        $this -> addFlash("point_vente_validate","le point vente \"".$point_vente->getNomPointVente(). "\" a été activé avec succès");


        return $this->redirectToRoute("point_ventes.index");
    }





    #[Route('/point_ventes/update/{id}', name: 'point_ventes.update')]
    public function update(Request $request,ManagerRegistry $doctrine,PointVenteRepository $pointVenteRepository,$id): Response
    {
        // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $entityManager = $doctrine->getManager();
        
        $point_vente = $pointVenteRepository->findOneByDistributeurField($id , $this->getUser()->getDistributeur()->getId());
     

        if( $point_vente==null) return $this->redirectToRoute("point_ventes.index");

      

        $form = $this->createForm(PointVenteUpdateType::class, $point_vente);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager ->flush();
            $this -> addFlash("point_vente_update","le point vente  a été modifié avec succès");
            return $this->redirectToRoute("point_ventes.index");
        }

     
        return $this->render('point_vente/update.html.twig', [
            'controller_name' => 'Business | modification de point de vente',
            'form' => $form->createView()
        ]);
    }



    #[Route('/point_ventes/abonnements', name: 'point_ventes.abonnements')]
    public function abonnements(ManagerRegistry $doctrine,AbonnementRepository $abonnementRepository): Response
    {   
        // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();
        
        $abonnements =  $abonnementRepository->findBydistributeurField( $this->getUser()->getDistributeur()->getId());
        return $this->render('point_vente/abonnement.html.twig', [
            'controller_name' => 'Business | distributeur | abonnements',
            "abonnements"=>$abonnements
        ]);


    }



    #[Route('/point_ventes/give/{id}', name: 'point_ventes.give')]
    public function give(Request $request,ManagerRegistry $doctrine,AbonnementRepository $abonnementRepository,$id): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();
        
        $n= $request->request->get("number");
        $entityManager= $doctrine->getManager();
        $point_vente = $doctrine->getRepository(PointVente::class)->find($id); 
        $abonnements =  $abonnementRepository->findByavalable( $this->getUser()->getDistributeur()->getId(),$n);
       
        if(count($abonnements)!=$n){  
                $this -> addFlash("point_vente_errors","Les abonnements n'ont pas été  attribué au point vente   avec succès");
                return $this->redirectToRoute("point_ventes.index");
            }
        foreach($abonnements as $abonnement ){

            $abonnement->setPointVente($point_vente);
            $entityManager->flush();
        }

        $this -> addFlash("point_vente_update","Les abonnements ont été  attribué au point vente   avec succès");
        return $this->redirectToRoute("point_ventes.index");


    }

    // #[Route('/point_vente/take', name: 'abonnements.take')]
    // public function take(ManagerRegistry $doctrine,AbonnementRepository $abonnementRepository): Response
    // {   
        
    //     $abonnements =  $abonnementRepository->findByPartenaireField(1);
    //     return $this->redirectToRoute("point_ventes.index");


    // }


}