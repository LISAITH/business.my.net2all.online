<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\Kits;
use App\Form\KitType;
use App\Repository\AbonnementRepository;
use App\Repository\KitsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class KitController extends AuthController
{
    protected  $type = 4;
    protected $codePays ="BJ";
    protected $cle1="N2A";
    protected $cle2="S01";

    #[Route('/kits', name: 'kits.index')]
    public function index(ManagerRegistry $doctrine, AbonnementRepository $kitsRepository): Response
    {    // verifier si l'utilisateur est un partenaire
        if (!$this->checkAuthType()) return $this->forbidden();

        $kits =  $kitsRepository->findBy(["distributeur"=>null]);
        return $this->render('kit/index.html.twig', [
            'controller_name' => 'Business | Les kits',
            "kits" => $kits
        ]);
    }







    #[Route('/kits/status/{id}/validate', name: 'kits.status.validate')]
    #[Route('/kits/status/{id}/unvalidate', name: 'kits.status.unvalidate')]
    public function status_unvalidate(ManagerRegistry $doctrine, AbonnementRepository $kitsRepository,$id): Response

    {   
        if (!$this->checkAuthType()) return $this->forbidden();


        $entityManager = $doctrine->getManager();

        $kit =  $kitsRepository->find($id);

        if(!$kit) {
            $this->addFlash("flash_error", "le kit introuvable");
            return $this->redirectToRoute("kits.index");
        }

        $status= !$kit->isStatus();
        $kit->setStatus($status);
        $entityManager->flush();

        if ($status) $this->addFlash("flash_success", "le kit est  activé avec succès");
        else $this->addFlash("flash_success", "le kit est maintenant désactivé avec succès");

        return $this->redirectToRoute("kits.index");
    }

 





    #[Route('/kits/create', name: 'kits.create')]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {   
        if ( !$this->checkAuthType() ) return $this->forbidden();

        $form = $this->createForm(KitType::class);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
          
            $entityManager = $doctrine->getManager();
            $today = date("dmY");
            $numbers = $form->getData()["number"];

            for ($i = 1; $i <= $numbers; $i++) {

                $kit = new Abonnement();

                $numeroSerie = $today 
                    . $this->getRamdomText2(6) 
                    . $this->codePays
                    . $this->cle1
                    . $this->cle2;

                $numeroActivation = $this->codePays 
                    . $this->getRamdomText(4)
                    . "-" . $this->getRamdomText(4) . "-" 
                    . $this->getRamdomText(4) . "-" 
                    . $this->getRamdomText(4)
                    . $this->cle1;

                $kit->setNumeroActivation($numeroActivation)
                    ->setNumeroSerie($numeroSerie)
                    ->setStatus(true);

                $entityManager->persist($kit);
            }

            $entityManager->flush();
            $this->addFlash("flash_success", "les kits ont été générés avec succès");

            return $this->redirectToRoute('kits.index');
        }

        return $this->render('kit/create.html.twig', [
            'controller_name' => 'Business | générer un kits',
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
