<?php

namespace App\Controller;

use App\Controller\AuthController;
use App\Entity\SouscriptionFormules;
use App\Entity\ActivationAbonnements;
use App\Form\EntrepriseSouscrireType;
use App\Repository\FormuleRepository;
use App\Repository\AbonnementRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SouscriptionFormulesRepository;
use App\Repository\ActivationAbonnementsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EntrepriseAreaController extends AuthController
{
    protected $type = 6;



    #[Route('/entreprise/formules', name: 'entreprise.formules')]
    public function formules(FormuleRepository $formuleRepository): Response
    {    // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $formules=$formuleRepository->findByActiveFields();
        return $this->render('entreprise_area/index.html.twig', [
            'controller_name' => 'Business |Listes des  formules',
            'formules'=>$formules,
        ]);
    }

    #[Route('/entreprise/abonnements', name: 'entreprise.abonnements')]
    public function abonnements(ManagerRegistry $doctrine, ActivationAbonnementsRepository $activationRepository): Response
    {    // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $activations =  $activationRepository->findByEntrepriseUser($this->getUser());
        return $this->render('entreprise_area/abonnements.html.twig', [
            'controller_name' => 'Business | Tous mes abonnements',
            "activations" => $activations
        ]);
    }


    #[Route('/entreprise/souscriptions/create', name: 'entreprise.souscriptions.create')]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {    // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $souscription = new SouscriptionFormules();
        $entityManager = $doctrine->getManager();
        $form = $this->createForm(EntrepriseSouscrireType::class, $souscription,["user"=>$this->getUser()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
         
            $souscription->setIsValidated(true);
            $entityManager->persist($souscription);
            $entityManager->flush();

            $this->addFlash("souscription_add", "la souscription  a été éffectué avec succès");
            return $this->redirectToRoute('souscriptions.index');
        }
        return $this->render('entreprise_area/souscription.html.twig', [
            'form' => $form->createView(),
            'controller_name' => "Business | Souscrire à une formule"
        ]);
    }


    #[Route('entreprise/abonnements/check', name: 'entreprise.abonnements.check')]
    public function index(Request $request, AbonnementRepository $abonnementRepository): Response
    {
        if ($request->getMethod() == "POST") {
            $input = $request->get('numcheck');
            $check = $abonnementRepository->findOneBy(['numero_activation' => $input]);
            if (!$check) return $this->render('entreprise_area/abonnement_check.html.twig', ['status' => '-1']);

            if ($check->isStatus()) {
                return $this->render('entreprise_area/abonnement_check.html.twig', ['status' => '1']);
            } else {
                return $this->render('entreprise_area/abonnement_check.html.twig', ['status' => '2']);
            }
        }

        return $this->render('entreprise_area/abonnement_check.html.twig', []);
    }
}
