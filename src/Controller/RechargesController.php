<?php

namespace App\Controller;

use App\Entity\Recharges;
use App\Form\RechargeType;
use App\Controller\AuthController;
use App\Repository\SousCompteRepository;
use App\Repository\ParticuliersRepository;
use App\Repository\EntreprisesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CompteEcashRepository;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Services\AppServices;

class RechargesController extends AuthController
{
    // protected $type=5;

    #[Route('/recharges/credite_sous_compte', name: 'app_recharges.credite_sous_compte')]
    public function credite_souscompte(Request $request,EntityManagerInterface $entityManager,SousCompteRepository $compte_sousRepository,CompteEcashRepository $compte_ecashRepository,FlashyNotifier $flashy): Response
    {
        if(!$this->checkAuthType()) return $this->forbidden();
        $point_vente=$this->getUser()->getPointVente();
        $recharge=new Recharges();
        $form = $this->createForm(RechargeType::class);
        $form->remove('numero_compte');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sous_compte=$compte_sousRepository->findOneBy(["numeroSousCompte"=>$form->get('numero_sous_compte')->getData()]);
            $compte_ecash=$compte_ecashRepository->findOneBy(["numeroCompte"=>$sous_compte->getCompteEcash()->getNumeroCompte()]);
            $montant=(double)$form->get('montant')->getData();
            
            if($compte_ecash->getSolde()>=$montant){

            // decredite un compte ecash
            $montant_debite=$compte_ecash->getSolde()-$montant;
            $compte_ecash->setSolde($montant_debite);
            $entityManager->persist($compte_ecash);

            // sous compte debite
            $montant_debite=$sous_compte->getSolde()+$montant;
            $sous_compte->setSolde($montant_debite);
            $entityManager->persist($sous_compte);

            }
            $entityManager->flush();
            $flashy->success(" SOus Compte débiter avec succès");
            return $this->redirectToRoute('app_recharges.credite_sous_compte');
        }
        return $this->render('recharges/crediter_sous_compte.html.twig', [
            'controller_name' => 'RechargesController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/recharges/credite_compte', name: 'app_recharges.credite_compte')]
    public function credite_compte(Request $request,EntityManagerInterface $entityManager,CompteEcashRepository $compte_ecashRepository,FlashyNotifier $flashy): Response
    {
        if($this->checkAuthType()) return $this->forbidden();
        $type = $this->getUser()->getType()->getId();
        $point_vente=$this->getUser()->getPointVente();
        $recharge=new Recharges();
        $form = $this->createForm(RechargeType::class);
        $form->remove('numero_sous_compte');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $compte_ecash=$compte_ecashRepository->findOneBy(["numeroCompte"=>$form->get('numero_compte')->getData()]);
            dd($compte_ecash);
            $montant=(double)$form->get('montant')->getData();
            if($montant>99 and $montant<=2000000){
                // compte Ecash debite
                $montant_debite=$compte_ecash->getSolde()+$montant;
                $compte_ecash->setSolde($montant_debite);
                $entityManager->persist($compte_ecash);

                //Recharges store
                $recharge->setPointVente($point_vente);
                $recharge->setCompteEcash($compte_ecash);
                $recharge->setMontant($montant);
                $recharge->setDateRecharge(new \DateTime());
                $entityManager->persist($recharge);
            }
            $entityManager->flush();
            $flashy->success("Compte Ecash débiter avec succès");
            return $this->redirectToRoute('app_recharges.credite_compte');
        }
        return $this->render('recharges/crediter_compte.html.twig', [
            'controller_name' => 'RechargesController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/recharges/credite/compte/bpay', name: 'app_recharges.credite_compte_bpay')]
    public function credite_compte_bpay(Request $request,EntityManagerInterface $entityManager,FlashyNotifier $flashy, AppServices $appServices, HttpClientInterface $httpClient): Response
    {
        if($this->checkAuthType()) return $this->forbidden();
        $type = $this->getUser()->getType()->getId();

        if ($type === 1) {
            $acteur=$this->getUser()->getParticuliers();
            //$typeBeneficiaire = ;
        } elseif ($type === 2) {
            $acteur=$this->getUser()->getDistributeur();
            $typeBeneficiaire = ["pointVente" => 5];
        } elseif ($type === 3) {    
            $acteur=$this->getUser()->getPartenaire();
            $typeBeneficiaire = ["distributeur" => 2];
        } elseif ($type === 4) {
            // Pas encore de solution
        } elseif ($type === 5) {
            $acteur=$this->getUser()->getPointVente();
            $typeBeneficiaire = ["entreprise" => 6,"particulier" => 1];
        } elseif ($type === 6) {
            $acteur=$this->getUser()->getEntreprises();
            //$typeBeneficiaire = ;
        }

        $url = $appServices->getBpayServerAddress() . '/get/one/compte/Bpay/' . $acteur->getId() . '/' . $type;
        $responseCompteBpay = $httpClient->request('GET', $url, [
            'headers' => [
                'Content-Type: application/json',
                'Accept' => 'application/json',
            ]
        ]);
        $compteBpay = $responseCompteBpay->getContent();
        $compteBpay = json_decode($compteBpay, true);

        $form = $this->createFormBuilder( null, ['attr' => ['id' => 'form']] )
            ->add('solde',NumberType::class,[
                'attr' => [
                    'class' => 'form-control', 
                    'placeholder' => "Solde disponible", 
                    'disabled' => true
                ],
                'data' => $compteBpay["solde"],
            ])
            ->add('myNumeroCompte', HiddenType::class, [
                'attr' => [
                    'class' => 'form-control', 
                    'placeholder' => "Numéro de compte"
                ],
                'data' => $compteBpay["numeroCompte"],
                'mapped' => false,
            ])
            ->add('numero_compte',TextType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Numéro de compte"],
            ])
            ->add('name_compte',TextType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Nom & Prénom / Raison sociale", 'disabled' => true],
            ])
            ->add('montant',NumberType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Montant",'min'=>'100','max'=>'2000000'],
            ])

            ->getForm();

        $form->handleRequest($request);
        $recharge=new Recharges();
        // if ($form->isSubmitted() && $form->isValid()) {
        //     $compte_ecash=$compte_ecashRepository->findOneBy(["numeroCompte"=>$form->get('numero_compte')->getData()]);
        //     dd($compte_ecash);
        //     $montant=(double)$form->get('montant')->getData();
        //     if($montant>99 and $montant<=2000000){
        //         // compte Ecash debite
        //         $montant_debite=$compte_ecash->getSolde()+$montant;
        //         $compte_ecash->setSolde($montant_debite);
        //         $entityManager->persist($compte_ecash);

        //         //Recharges store
        //         $recharge->setPointVente($point_vente);
        //         $recharge->setCompteEcash($compte_ecash);
        //         $recharge->setMontant($montant);
        //         $recharge->setDateRecharge(new \DateTime());
        //         $entityManager->persist($recharge);
        //     }
        //     $entityManager->flush();
        //     $flashy->success("Compte Ecash débiter avec succès");
        //     return $this->redirectToRoute('app_recharges.credite_compte');
        // }
        return $this->render('recharges/crediter_compte_bpay.html.twig', [
            'controller_name' => 'RechargesController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/searchNameAccount', name: 'searchNameAccount', options: ['expose' => true])]
    public function searchNameAccount(Request $request,CompteEcashRepository $compte_ecashRepository, ParticuliersRepository $particulierRepository, EntreprisesRepository $entrepriseRepository): JsonResponse
    {
        $compte_ecash=$compte_ecashRepository->findOneBy(["numeroCompte"=>$request->request->get("numero_compte")]);
        if($compte_ecash){
            if($compte_ecash->getEntreprise()){
                $entreprise = $entrepriseRepository->findOneBy(["id" => $compte_ecash->getEntreprise()->getId()]);
                $name = $entreprise->getNomEntreprise();
                $phone = $entreprise->getNumTel();
            }elseif($compte_ecash->getParticulier()){
                $particulier = $particulierRepository->findOneBy(["id" => $compte_ecash->getParticulier()->getId()]);
                $name = $particulier->getNom()." ".$particulier->getPrenoms();
                $phone = $particulier->getNumTel();
            }
        }else{
            $name = "";
            $phone = "";
        }
        $results = [
            'name' => $name,
            'phone' => $phone
        ];
        return new JsonResponse($results);
    }
}