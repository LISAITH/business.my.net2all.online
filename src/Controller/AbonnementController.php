<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\Partenaire;
use App\Form\AbonnementType;
use App\Controller\AuthController;
use App\Repository\AbonnementRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AbonnementController extends AuthController
{
    protected  $type = 3;

    #[Route('/abonnements', name: 'abonnements.index')]
    public function index(ManagerRegistry $doctrine, AbonnementRepository $abonnementRepository): Response
    {    // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $abonnements =  $abonnementRepository->findByPartenaireField($this->getUser()->getPartenaire()->getId());
        return $this->render('abonnement/index.html.twig', [
            'controller_name' => 'AbonnementController',
            "abonnements" => $abonnements
        ]);
    }

    // #[Route('/abonnements/test', name: 'abonnements.test')]
    // public function test(ManagerRegistry $doctrine, AbonnementRepository $abonnementRepository): Response
    // {
    //     // verifier si l'utilisateur est un partenaire
    //     if(!$this->checkAuthType()) return $this->forbidden();

    //     $abonnements = $abonnementRepository->findOneByPartenaireField(1, 1);
    //     dd($abonnements);
    //     return $this->render('abonnement/index.html.twig', [
    //         'controller_name' => 'AbonnementController',
    //         "abonnements" => $abonnements
    //     ]);
    // }





    #[Route('/abonnements/status/{id}/unvalidate', name: 'abonnements.status.unvalidate')]
    public function status_unvalidate(ManagerRegistry $doctrine, AbonnementRepository $abonnementRepository, $id): Response

    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();


        $entityManager = $doctrine->getManager();

        $abonnement =  $abonnementRepository->findOneByPartenaireField($id, $this->getUser()->getPartenaire()->getId());
        $abonnement->setStatus(false);

        $entityManager->flush();

        return $this->redirectToRoute("abonnements.index");
    }

    #[Route('/abonnements/status/{id}/validate', name: 'abonnements.status.validate')]
    public function status_validate(ManagerRegistry $doctrine, AbonnementRepository $abonnementRepository,  $id): Response

    {
          // verifier si l'utilisateur est un partenaire
          if(!$this->checkAuthType()) return $this->forbidden();


          $entityManager = $doctrine->getManager();
  
          $abonnement =  $abonnementRepository->findOneByPartenaireField($id, $this->getUser()->getPartenaire()->getId());
        $abonnement->setStatus(true);

        $entityManager->flush();

        return $this->redirectToRoute("abonnements.index");
    }





    #[Route('/abonnements/create', name: 'abonnements.create')]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {    // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();



        $form = $this->createForm(AbonnementType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $id = 1;
            $entityManager = $doctrine->getManager();
            $partenaire = $this->getUser()->getPartenaire();
            $distributeur = $form->getData()["distributeur"];
            $today = date("dmY");
            $numbers = $form->getData()["number"];

            for ($i = 1; $i <= $numbers; $i++) {

                $abonnement = new Abonnement();

                $numeroSerie = $today . $this->getRamdomText2(6) . "" . $partenaire->getCodePays()
                    . "" . $partenaire->getClePartenaire() . "" . $distributeur->getCleDistributeur();

                $numeroActivation = $partenaire->getCodePays() . "" . $this->getRamdomText(4)
                    . "-" . $this->getRamdomText(4) . "-" . $this->getRamdomText(4) . "-" . $this->getRamdomText(4)
                    . "" . $partenaire->getClePartenaire();

                $abonnement->setDistributeur($distributeur)->setNumeroActivation($numeroActivation)
                    ->setNumeroSerie($numeroSerie)->setStatus(true);

                $entityManager->persist($abonnement);
            }

            $entityManager->flush();
            $this->addFlash("abonnement_add", "les abonnement ont été générés avec succès");

            return $this->redirectToRoute('abonnements.index');
        }

        return $this->render('abonnement/create.html.twig', [
            'controller_name' => 'AbonnementController',
            "form" => $form->createView()




        ]);
    }


    function getRamdomText($n)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    function getRamdomText2($n)
    {
        $characters = '0123456789';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    function getRamdomText1($n)
    {
        $characters = 'AZERTYUIOPQSDFGHJKLMWXCVBN';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }


    function getRamdomText3($n)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}