<?php

namespace App\Controller;

use App\Entity\Type;
use App\Entity\User;
use App\Entity\Partenaire;
use App\Entity\Distributeur;
use App\Form\DistributeurType;
use App\Controller\AuthController;
use App\Repository\TypeRepository;
use App\Form\DistributeurUpdateType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Services\AppServices;

class DistributeurController extends AuthController
{
    protected $type=3;

    #[Route('/distributeurs', name: 'distributeurs.index')]
    public function index(ManagerRegistry $doctrine): Response

    {  // verifier si l'utilisateur est un partenaire = 
        if(!$this->checkAuthType()) return $this->forbidden();

        $partenaire=$this->getUser()->getPartenaire();
        $distributeurs=$partenaire->getDistributeurs();
        return $this->render('distributeur/index.html.twig', [
            'controller_name' => 'Business | Listes distributeurs',
            "distributeurs"=>$distributeurs
        ]);
    }

    #[Route('/distributeur/status/{id}/unvalidate', name: 'distributeurs.status.unvalidate')]
    public function status_unvalidate(ManagerRegistry $doctrine,  $id): Response

    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();
        
        $entityManager = $doctrine->getManager();
        
        
        $user = $entityManager->getRepository(User::class)->find($id);
        $user->setStatus(false);
        
        $entityManager->flush();

        return $this->redirectToRoute("distributeurs.index");
    }

    #[Route('/distributeur/status/{id}validate', name: 'distributeurs.status.validate')]
    public function status_validate(ManagerRegistry $doctrine,  $id): Response

    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();


        $entityManager = $doctrine->getManager();
        
        
        $user = $entityManager->getRepository(User::class)->find($id);
        $user->setStatus(true);
        
        $entityManager->flush();

        return $this->redirectToRoute("distributeurs.index");
    }

    #[Route('/distributeurs/test', name: 'distributeurs.test')]
    public function test(Request $request): Response
    {

        
        $distributeur = new Distributeur();
        $form = $this->createForm(DistributeurType::class, $distributeur);
        $form->handleRequest($request);

       

        if ($form->isSubmitted() && $form->isValid()) {
            
            return $this->redirectToRoute("distributeurs.index");
        }

        
        
        return $this->render('distributeur/test.html.twig', [
            'controller_name' => 'DistributeurController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/distributeurs/create', name: 'distributeurs.create')]
    public function create(Request $request,ManagerRegistry $doctrine,UserPasswordHasherInterface $userPasswordHasher, AppServices $appServices, HttpClientInterface $httpClient): Response
    {
        // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $type = $doctrine->getRepository(Type::class)->find(2);
        $user = new User();
        $user->setType($type)->setStatus(true);
        $distributeur = new Distributeur();
        $distributeur->setUser($user);
        $distributeur->setPartenaire($this->getUser()->getPartenaire());
        $form = $this->createForm(DistributeurType::class, $distributeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $distributeur->getUser()->setPassword($userPasswordHasher->hashPassword($user,$user->getPassword()));
            $entityManager = $doctrine->getManager();
            $entityManager->persist($distributeur);
            $entityManager ->flush();

            $url = $appServices->getBpayServerAddress() . '/create/compte/Bpay/' . $distributeur->getId() . '/' . $type->getId();
			$response = $httpClient->request('POST', $url, [
                'headers' => [
                    'Content-Type: application/json',
                    'Accept' => 'application/json',
                ]
            ]);
            $content = $response->getContent();
           
            return $this->redirectToRoute("distributeurs.index");
        }

     
        
        return $this->render('distributeur/create.html.twig', [
            'controller_name' => 'DistributeurController',
            'form' => $form->createView()
        ]);
    }



    #[Route('/distributeurs/update/{id}', name: 'distributeurs.update')]
    public function update(Request $request,ManagerRegistry $doctrine,$id): Response
    {
        // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $entityManager = $doctrine->getManager();
        
        $user = $entityManager->getRepository(User::class)->find($id);

        if($user==null) return $this->redirectToRoute("distributeurs.index");

        $distributeur = $user->getDistributeur();

        $form = $this->createForm(DistributeurUpdateType::class, $distributeur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager ->flush();
            return $this->redirectToRoute("distributeurs.index");
        }

     
        return $this->render('distributeur/update.html.twig', [
            'controller_name' => 'Business',
            'form' => $form->createView()
        ]);
    } 
}