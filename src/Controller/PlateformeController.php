<?php

namespace App\Controller;

use App\Entity\Type;
use App\Entity\User;
use App\Entity\Plateforme;
use App\Entity\CompteEcash;
use App\Form\PlateformeType;
use App\Controller\AuthController;
// use App\Form\PartenaireUpdateType;
use Doctrine\Persistence\ObjectManager;
use App\Repository\PlateformeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Services\AppServices;

class PlateformeController extends AuthController
{   
    protected $type = 7;

    #[Route('/plateforme', name: 'plateforme.index')]
    public function index(PlateformeRepository $plat): Response
    {  
        // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $plateforme=$plat->findAll();
        return $this->render('plateforme/index.html.twig', [
            'controller_name' => 'DistributeurController',
            'plateforme'=>$plateforme
        ]);
    }


    #[Route('/add_plateforme', name: 'add_plateforme')]
    public function create(Request $request,ManagerRegistry $doctrine,UserPasswordHasherInterface $userPasswordHasher, AppServices $appServices, HttpClientInterface $httpClient): Response
    {
        // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();
        $type = $doctrine->getRepository(Type::class)->find(3);
        $partenaire=new Partenaire();
        $user=new User();
        $user->setType($type)->setStatus(true);
        $partenaire->setUser($user);
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $partenaire->getUser()->setPassword($userPasswordHasher->hashPassword($user,$user->getPassword()));
            $partenaire->setCodePays($request->request->get('code_pays'));
            $entityManager = $doctrine->getManager();
            $entityManager->persist($partenaire);            
            $entityManager ->flush();

            $url = $appServices->getBpayServerAddress() . '/create/compte/Bpay/' . $partenaire->getId() . '/' . $type->getId();
			$response = $httpClient->request('POST', $url, [
                'headers' => [
                    'Content-Type: application/json',
                    'Accept' => 'application/json',
                ]
            ]);
            $content = $response->getContent();

            return $this->redirectToRoute("partenaire.index");
       }
        return $this->render('partenaire/create.html.twig', [
            'controller_name' => 'PartenaireController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/partenaire/desactive/{id}', name: 'partenaire.invalidate')]
    public function status_invalidate(ManagerRegistry $doctrine,  $id): Response

    {   
        // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        $user->setStatus(false);
        $entityManager->flush();
        return $this->redirectToRoute("partenaire.index");
    }

    #[Route('/partenaire/status/{id}', name: 'partenaire.validate')]
    public function status_validate(ManagerRegistry $doctrine,  $id): Response

    {  
        // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        $user->setStatus(true);
        $entityManager->flush();

        return $this->redirectToRoute("partenaire.index");
    }

    #[Route('/partenaire/update/{id}', name: 'partenaire.update')]
    public function update(Request $request,ManagerRegistry $doctrine,$id): Response
    {
        // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();
        

        $entityManager = $doctrine->getManager();
        
        $user = $entityManager->getRepository(User::class)->find($id);

        if($user==null) return $this->redirectToRoute("partenaire.index");

        $partenaire = $user->getPartenaire();
        $form = $this->createForm(PartenaireUpdateType::class, $partenaire );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager ->flush();
            return $this->redirectToRoute("partenaire.index");
        }

     
        return $this->render('partenaire/update.html.twig', [
            'controller_name' => 'PartenaireController',
            'form' => $form->createView()
        ]);
    }
}