<?php

namespace App\Controller;

use App\Entity\PointVente;
use App\Form\SouscriptionType;
use App\Controller\AuthController;
use App\Entity\SouscriptionFormules;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SouscriptionFormulesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SouscriptionFormuleController extends AuthController
{   protected $type=5;

    #[Route('/souscriptions', name: 'souscriptions.index')]
    public function showList(SouscriptionFormulesRepository $souscriptionRepository): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $souscription = $this->getUser()->getPointVente()->getSouscriptionFormules();
        return $this->render('vente/souscriptionList.html.twig', [
            "souscriptions" => $souscription
        ]);
    }

    #[Route('/souscriptions/create', name: 'souscriptions.create')]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {    // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $souscription = new SouscriptionFormules();
        $entityManager = $doctrine->getManager();
        $form = $this->createForm(SouscriptionType::class, $souscription);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pointVente = $this->getUser()->getPointVente();
            $souscription->setPointVente($pointVente);
            $souscription->setIsValidated(true);
            $entityManager->persist($souscription);
            $entityManager->flush();

            $this->addFlash("souscription_add", "la souscription  a été éffectué avec succès");

            return $this->redirectToRoute('souscriptions.index');
        }
        return $this->render('vente/souscription.html.twig', [
            'form' => $form->createView(),
            'controller_name' => "Business | Ajouter service au formule"
        ]);
    }
}