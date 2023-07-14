<?php

namespace App\Controller;

use App\Entity\Recharges;
use App\Entity\Partenaire;
use App\Entity\Distributeur;
use App\Entity\PointVente;
use App\Form\RechargeType;
use App\Controller\AuthController;
use App\Repository\SousCompteRepository;
use App\Repository\ParticuliersRepository;
use App\Repository\EntreprisesRepository;
use App\Repository\PartenaireRepository;
use App\Repository\DistributeurRepository;
use App\Repository\PointVenteRepository;
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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
    public function credite_compte_bpay(Request $request,EntityManagerInterface $entityManager,FlashyNotifier $flashy, AppServices $appServices, HttpClientInterface $httpClient, PartenaireRepository $partenaireRepository, DistributeurRepository $distributeurRepository, PointVenteRepository $pointVenteRepository): Response
    {
        if($this->checkAuthType()) return $this->forbidden();
        $type = $this->getUser()->getType()->getId();
        $saisiNumCompte = false;

        if ($type === 2) {
            $title = "point de vente";
            $acteur=$this->getUser()->getDistributeur();
            $typeBeneficiaire = 5;
            $beneficiaire = $pointVenteRepository->findByDistributeurField($acteur->getId());
        } elseif ($type === 3) { 
            $title = "distributeur";
            $acteur=$this->getUser()->getPartenaire();
            $typeBeneficiaire = 2;
            $beneficiaire = $acteur->getDistributeurs();
        } elseif ($type === 4) {
            $acteur=null;
            $typeBeneficiaire = 3;
            $beneficiaire = $partenaireRepository->findAll();
        } elseif ($type === 5) {
            $acteur=$this->getUser()->getPointVente();
            $saisiNumCompte = true;
        }

        if($acteur){
            $url = $appServices->getBpayServerAddress() . '/get/one/compte/Bpay/' . $acteur->getId() . '/' . $type;
            $responseCompteBpay = $httpClient->request('GET', $url, [
                'headers' => [
                    'Content-Type: application/json',
                    'Accept' => 'application/json',
                ]
            ]);
            $accountRetrait = $responseCompteBpay->getContent();
            $accountRetrait = json_decode($accountRetrait, true);

            if ($type === 2) {
                $form = $this->createFormBuilder( null, ['attr' => ['id' => 'form']] )
                ->add('solde',NumberType::class,[
                    'attr' => [
                        'class' => 'form-control', 
                        'placeholder' => "Solde disponible", 
                        'disabled' => true
                    ],
                    'data' => $accountRetrait["solde"],
                ])
                ->add('destinataire', EntityType::class, [
                    'class' => PointVente::class,
                    'attr' => [
                        'class' => 'form-control select_simple',
                    ],
                    'label' => 'Partenaire',
                    'placeholder'=>'Sélectionner un point de vente',
                    'choices' => $beneficiaire
                ])
                ->add('montant',NumberType::class,[
                    'attr' => ['class' => 'form-control', 'placeholder' => "Montant",'min'=>'100','max'=>'2000000'],
                ])
                ->getForm();
            } elseif ($type === 3) {    
                $form = $this->createFormBuilder( null, ['attr' => ['id' => 'form']] )
                ->add('solde',NumberType::class,[
                    'attr' => [
                        'class' => 'form-control', 
                        'placeholder' => "Solde disponible", 
                        'disabled' => true
                    ],
                    'data' => $accountRetrait["solde"],
                ])
                ->add('destinataire', EntityType::class, [
                    'class' => Distributeur::class,
                    'attr' => [
                        'class' => 'form-control select_simple',
                    ],
                    'label' => 'Partenaire',
                    'placeholder'=>'Sélectionner un distributeur',
                    'choices' => $beneficiaire
                ])
                ->add('montant',NumberType::class,[
                    'attr' => ['class' => 'form-control', 'placeholder' => "Montant",'min'=>'100','max'=>'2000000'],
                ])
                ->getForm();
            } elseif ($type === 5) {
                $form = $this->createFormBuilder( null, ['attr' => ['id' => 'form']] )
                ->add('solde',NumberType::class,[
                    'attr' => [
                        'class' => 'form-control', 
                        'placeholder' => "Solde disponible", 
                        'disabled' => true
                    ],
                    'data' => $accountRetrait["solde"],
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
            }
        }else{
            $url = $appServices->getBpayServerAddress() . '/get/one/compte/Bpay/6/7';
            $responseCompteBpay = $httpClient->request('GET', $url, [
                'headers' => [
                    'Content-Type: application/json',
                    'Accept' => 'application/json',
                ]
            ]);
            $accountRetrait = $responseCompteBpay->getContent();
            $accountRetrait = json_decode($accountRetrait, true);

            $form = $this->createFormBuilder( null, ['attr' => ['id' => 'form']] )
            ->add('destinataire', EntityType::class, [
                'class' => Partenaire::class,
                'attr' => [
                    'class' => 'form-control select_simple',
                ],
                'label' => 'Partenaire',
                'placeholder'=>'Sélectionner un partenaire',
                'choices' => $beneficiaire
            ])
            ->add('montant',NumberType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Montant",'min'=>'100','max'=>'2000000'],
            ])

            ->getForm();
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if($saisiNumCompte){
                $url = $appServices->getBpayServerAddress() . '/get/one/compte/Bpay/by/number/account/'. $form->get('numero_compte')->getData();
                $responseCompteBpay = $httpClient->request('GET', $url, [
                    'headers' => [
                        'Content-Type: application/json',
                        'Accept' => 'application/json',
                    ]
                ]);
                $accountDepot = $responseCompteBpay->getContent();
                $accountDepot = json_decode($accountDepot, true);
            }else{
                $url = $appServices->getBpayServerAddress() . '/get/one/compte/Bpay/' . $form->get('destinataire')->getData()->getId() . '/' . $typeBeneficiaire;
                $responseCompteBpay = $httpClient->request('GET', $url, [
                    'headers' => [
                        'Content-Type: application/json',
                        'Accept' => 'application/json',
                    ]
                ]);
                $accountDepot = $responseCompteBpay->getContent();
                $accountDepot = json_decode($accountDepot, true);
            }

            $url = $appServices->getBpayServerAddress() . '/virement/compte/Bpay/' . $accountDepot["id"] . '/' . $accountRetrait["id"] . '/' . (double)$form->get('montant')->getData();
            $response = $httpClient->request('POST', $url, [
                'headers' => [
                    'Content-Type: application/json',
                    'Accept' => 'application/json',
                ]
            ]);
            $content = $response->getContent();
            
            $flashy->success("Compte crédité avec succès");
            return $this->redirectToRoute('app_recharges.credite_compte_bpay');
        }

        if($acteur){
            if($saisiNumCompte){
                return $this->render('recharges/crediter_compte_bpay.html.twig', [
                    'controller_name' => 'RechargesController',
                    'form' => $form->createView()
                ]);
            }else{
                return $this->render('recharges/crediter_compte_bpay_organe.html.twig', [
                    'controller_name' => 'RechargesController',
                    'title' => $title,
                    'form' => $form->createView()
                ]);
            }
        }else{
            return $this->render('recharges/crediter_compte_bpay_partenaire.html.twig', [
                'controller_name' => 'RechargesController',
                'form' => $form->createView()
            ]);
        }
        
    }

    #[Route('/searchNameAccount', name: 'searchNameAccount', options: ['expose' => true])]
    public function searchNameAccount(Request $request, AppServices $appServices, HttpClientInterface $httpClient, ParticuliersRepository $particulierRepository, EntreprisesRepository $entrepriseRepository): JsonResponse
    {
        $url = $appServices->getBpayServerAddress() . '/get/one/compte/Bpay/by/number/account/'. $request->request->get("numero_compte");
        $responseCompteBpay = $httpClient->request('GET', $url, [
            'headers' => [
                'Content-Type: application/json',
                'Accept' => 'application/json',
            ]
        ]);
        $compte_ecash = $responseCompteBpay->getContent();
        $compte_ecash = json_decode($compte_ecash, true);
        
        if($compte_ecash){
            if($compte_ecash["entreprise_id"]){
                $entreprise = $entrepriseRepository->findOneBy(["id" => $compte_ecash["entreprise_id"]]);
                $name = $entreprise->getNomEntreprise();
            }elseif($compte_ecash["particulier_id"]){
                $particulier = $particulierRepository->findOneBy(["id" => $compte_ecash["particulier_id"]]);
                $name = $particulier->getNom()." ".$particulier->getPrenoms();
            }else{
                $name = "";
            }
        }else{
            $name = "";
        }
        $results = [
            'name' => $name,
        ];
        return new JsonResponse($results);
    }
}