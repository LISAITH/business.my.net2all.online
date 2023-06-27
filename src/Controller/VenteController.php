<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\PointVente;
use App\Controller\AuthController;
use App\Entity\ActivationAbonnements;
use App\Repository\FormuleRepository;
use App\Form\ActivationAbonnementType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ActivationAbonnementsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class VenteController extends AuthController
{     protected $type=5;
    #[Route('/ventes', name: 'ventes.index')]
    public function index(FormuleRepository $formuleRepository): Response
    {    // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $formules=$formuleRepository->findByActiveFields();
        return $this->render('vente/index.html.twig', [
            'controller_name' => 'Business |Listes des  formules',
            'formules'=>$formules,
        ]);
    }

    #[Route('/ventes/abonnement/activate', name: 'ventes.abonnement.activate')]
    public function activate(Request $request,ManagerRegistry $doctrine): Response
    {    // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $entityManager =$doctrine->getManager();
        $activation = new ActivationAbonnements();
        $point_vente = $this->getUser()->getPointVente();
        $form = $this->createForm(ActivationAbonnementType::class,$activation, ["point_vente" => $point_vente]);
       
        $form->handleRequest($request);

       

        if ($form->isSubmitted() && $form->isValid()) {

            $activation->setPointVente($point_vente);
            $entityManager->persist( $activation);
            $entityManager ->flush();
           
            return $this->redirectToRoute("ventes.index");
        }

     
        
        return $this->render('vente/activate.html.twig', [
            'controller_name' => "Business | Activer l'Abonnement" ,
            'form' => $form->createView()
        ]);
    }

    #[Route('/ventes/abonnements/activated', name: 'ventes.abonnements')]
    public function abonnements(ActivationAbonnementsRepository $ActivationAbonnementRepository): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        return $this->render('vente/activatedListAbonnements.html.twig', [

            "abonnementActivated" => $ActivationAbonnementRepository->findByPointVente($this->getUser()->getPointVente()->getId())
        ]);
    }

}
