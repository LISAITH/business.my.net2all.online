<?php

namespace App\Controller;

use DateTime;
use Exception;
use App\Entity\Recharges;
use App\Entity\CompteEcash;
use App\Entity\VirementsEcash;
use App\Controller\AuthController;
use App\Form\ProfilProspectionType;
use App\Entity\ProspectionPaiements;
use App\Repository\CompteEcashRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ParticuliersRepository;
use App\Repository\ProspectionsRepository;
use App\Entity\ParticulierProfilProspections;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ValueProspectionsRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProfilProspectionsRepository;
use App\Repository\ProspectionPaiementsRepository;
use App\Repository\ParticulierProfilProspectionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilProspectionController extends AuthController
{
    protected $type = 1;


    #[Route('/profile/prospections', name: 'profile.prospections.index')]
    public function index(
        Request $request,
        ParticuliersRepository $particuliersrepository,
        ProfilProspectionsRepository $profilProspectionsRepository,
        ProspectionsRepository $prospectionsRepository,

        ValueProspectionsRepository $valueRepository
    ): Response {
        if (!$this->checkAuthType()) return $this->forbidden();
        $parent = $this->getUser()->getParticuliers()[0];
        $userProfil = $parent->getProfilId();
        if (!$userProfil || $userProfil == 1) return $this->forbidden();
        $tauxProspectionJours = $prospectionsRepository->findProsDay($valueRepository->valueBy("pros/j"));



        $particuliers = $particuliersrepository->findByProspectManager($parent->getId());
        return $this->render('profil_prospection/index.html.twig', [
            'controller_name' => 'Business | Mon équipe de prospection',
            'particuliers' => $particuliers,
            "tauxProspectionJours" => $tauxProspectionJours
        ]);
    }

    #[Route('/profile/prospections/renumeration', name: 'profile.prospections.remuneration')]
    public function renumeration(
        Request $request,
        ParticuliersRepository $particuliersrepository,
        ProfilProspectionsRepository $profilProspectionsRepository,
        ProspectionsRepository $prospectionsRepository,
        ValueProspectionsRepository $valueRepository,
        ManagerRegistry $doctrine,
        ProspectionPaiementsRepository $paiementsRepository,
        CompteEcashRepository $compteEcashRepository,

    ): Response {

        //nombre de jour ouvrable dans le mois
        $ndays = $prospectionsRepository->getDays(date("Y-m-d"));

        if (!$this->checkAuthType()) return $this->forbidden();
        $parent = $this->getUser()->getParticuliers()[0];
        $userProfil = $parent->getProfilId();
        $particuliers = $particuliersrepository->findByProspectCommercial($parent->getId());



        if (!$userProfil || $userProfil != 5) return $this->forbidden();
        $date = new DateTime();
        $current_option = "";
        $moisActifs = $prospectionsRepository->findAcitveMonth();

        if ($request->query->has("month")) {
            if (key_exists($request->query->get("month"), $moisActifs)) {
                $date = $moisActifs[$request->query->get("month")];
                $current_option = $request->query->get("month");
                $day_Y_m_d = substr_replace($current_option, '-', 4, 0) . "-2";
                $ndays = $prospectionsRepository->getDays($day_Y_m_d);
            }
        }

        $tauxProspectionMois = $prospectionsRepository->findProsMensuel($valueRepository->valueBy("pros/j") * $ndays, $date);
        $renumerationMensuels = [];
        $prospectionSupplementaires = [];
        $paieDuMois = [];


        foreach ($tauxProspectionMois as $key => $value) {
            $calcul1 = ($value - 100) * $valueRepository->valueBy("pros/j") * $ndays / 100;
            $prospectionSupplementaires[$key] = (int) ($calcul1 > 0 ? $calcul1 : 0);
        }

        foreach ($tauxProspectionMois as $key => $value) {
            $calcul2 = $value > 100 ? 100 : $value;
            $renumerationMensuels[$key] =
                ($valueRepository->valueBy("remu/M") * $calcul2 / 100)
                + $prospectionSupplementaires[$key] * $valueRepository->valueBy("montant/pros_suppl");
        }




        $payements_success = true;
        $nombreApayer=0; 
        //payement des renumeration
        $entityManager = $doctrine->getManager();
        if ($request->isMethod('POST')) {

            $commercials[] = $particuliersrepository->findByOneProspectCommercial($parent->getId(), $request->request->get('id'));
            if ($request->request->get('id') == "") $commercials = $particuliers;
            $yearmonth = $request->request->get("yearmonth");


            foreach ($commercials as $commercial) {
                $paiement = $paiementsRepository->findOneBy(["annee_mois" => $yearmonth, "particulier" => $commercial]);
                if ($paiement){
                    $this->addFlash("flash_error", "Echec de paiement, paiement déjà effectué");
                    continue;
                }

                if (!key_exists($commercial->getId(), $renumerationMensuels)){
                    $nombreApayer++;
  
                    
                    if(count($commercials) ==$nombreApayer){
                        $payements_success=false;
                        $this->addFlash("flash_error", "Echec de paiement, aucun paiement à effectuer");

                        break;
                    }

                    continue;
                }


               
                if ( $commercial && key_exists($yearmonth, $moisActifs)) {
                    $compte_ecash = $commercial->getCompteEcash();


                    // prospection paiement
                    $montant = $renumerationMensuels[$commercial->getId()];
                    $paie = new ProspectionPaiements();
                    $paie->setAnneeMois($yearmonth);
                    $paie->setMontant($montant);
                    $paie->setParticulier($commercial);
                    $paie->setDoneAt(new DateTime());
                    $entityManager->persist($paie);

                    // compte Ecash debite



                    $EcashCompteEnvoyeurFound = $compteEcashRepository->findOneBy(["numeroCompte" => $valueRepository->valueBy("ecash")]);
                    $EcashCompteReceveurFound =  $compte_ecash;
                    if ($EcashCompteEnvoyeurFound && $EcashCompteEnvoyeurFound->getSolde() >= (float)$montant) {
                        // decrediter le sous compte envoyeur
                        $decrediteEcashCompteEnvoyer = $compteEcashRepository->find($EcashCompteEnvoyeurFound->getId());
                        $decredite = $decrediteEcashCompteEnvoyer->getSolde() - (float)$montant;
                        $decrediteEcashCompteEnvoyer->setSolde($decredite);
                        $entityManager->persist($decrediteEcashCompteEnvoyer);

                        // créditer le sous compte receveur
                        $crediteEcashCompteReceveur = $compteEcashRepository->find($EcashCompteReceveurFound->getId());
                        $credite = $crediteEcashCompteReceveur->getSolde() + (float)$montant;
                        $crediteEcashCompteReceveur->setSolde($credite);
                        $entityManager->persist($crediteEcashCompteReceveur);

                        //store virement Ecash
                        $virement_ecash = new VirementsEcash();
                        $virement_ecash->setIdCompteEnvoyeur($EcashCompteEnvoyeurFound);
                        $virement_ecash->setIdCompteReceveur($EcashCompteReceveurFound);
                        $virement_ecash->setMontant((float)$montant);
                        $virement_ecash->setDateTransaction(new \DateTime());
                        $entityManager->persist($virement_ecash);


                        // dd($decrediteSousCompteEnvoyer);
                    } else {
                        if (!$EcashCompteEnvoyeurFound)  $this->addFlash("flash_error", "Echec de paiement, compte ecash introuvable");
                        else  $this->addFlash("flash_error", "Echec de paiement, fond insuffisant");
                        $payements_success = false;
                        break;
                    }
                } else {
                    $this->addFlash("flash_error", "Echec de paiement, commercial introuvable");
                }
            }

            if ($payements_success) {
                // try {
                    $entityManager->flush();
                    $this->addFlash("flash_success", "Paiement effectué avec succès");
                // } catch (Exception) {
                //     $this->addFlash("flash_error", "Echec de paiement");
                // }
            }
        }



        if (!empty($moisActifs)) {
            $anneeMois = $current_option !== "" ? $current_option : array_key_first($moisActifs);
            $paieDuMois = $paiementsRepository->findByAnneeMois($anneeMois);
        }


        return $this->render('profil_prospection/remuneration.html.twig', [
            'controller_name' => 'Business | Rémunération des prospections',
            'particuliers' => $particuliers,
            "tauxProspectionMois" => $tauxProspectionMois,
            "prospectionSupplementaires" => $prospectionSupplementaires,
            "renumerationMensuels" => $renumerationMensuels,
            "moisActifs" => $moisActifs,
            "paieDuMois" => $paieDuMois,
            "current_option" => $current_option
        ]);
    }


    #[Route('/profile/prospections/day', name: 'profile.prospection.day')]
    public function get_day(Request $request, ParticuliersRepository $particuliersrepository, ProfilProspectionsRepository $profilProspectionsRepository): Response
    {
        if (!$this->checkAuthType()) return $this->forbidden();
        $parent = $this->getUser()->getParticuliers()[0];
        $userProfil = $parent->getProfilId();
        if (!$userProfil || $userProfil == 1) return $this->forbidden();

        $particuliers = $particuliersrepository->findByProspectManager($parent->getId());


        return $this->render('profil_prospection/index.html.twig', [
            'controller_name' => 'Business | Mon équipe de prospection',
            'particuliers' => $particuliers,
        ]);
    }

    #[Route('/profile/prospections/create', name: 'profile.prospections.create')]
    public function create(
        Request $request,
        ManagerRegistry $doctrine,
        ProfilProspectionsRepository $profilProspectionsRepository
    ): Response {
        if (!$this->checkAuthType()) return $this->forbidden();
        $parent = $this->getUser()->getParticuliers()[0];
        $userProfil = $parent->getProfilId();
        if (!$userProfil || $userProfil == 1) return $this->forbidden();


        $entityManager = $doctrine->getManager();
        $profil = new ParticulierProfilProspections();
        $form = $this->createForm(ProfilProspectionType::class, $profil, ["profil_id" => $userProfil]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profil->setParent($parent);
            $entityManager->persist($profil);
            $profil->setParentParent($parent->getParticulierProfilProspections()->getParentParent() . "_" . $profil->getParticulier()->getId());
            $entityManager->flush();
            $this->addFlash("flash_success", "Le profil a été attribué avec succès");

            return $this->redirectToRoute('profile.prospections.index');
        }

        return $this->render('profil_prospection/create.html.twig', [
            'form' => $form->createView(),
            'controller_name' => "Business | Attribuer un profil de prospection",

        ]);
    }
}
