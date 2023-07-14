<?php

namespace App\Controller;

use App\Entity\Services;
use App\Form\ServiceType;
use App\Controller\AuthController;
use App\Repository\ServicesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Services\AppServices;

class ServiceController extends AuthController
{   protected $type=4;



    #[Route('/service', name: 'app_service')]
    public function index(ServicesRepository $service): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $services=$service->findAll();
        // dd( $services);
        return $this->render('service/index.html.twig', [
            'controller_name' => 'ServiceController',
            'services'=>$services
        ]);
    }

    #[Route('/addservice', name: 'add_service')]
    public function create(Request $request,ManagerRegistry $doctrine, AppServices $appServices, HttpClientInterface $httpClient): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        // $urlImage='ImageService/'.$nomService.'.jpg';
        // $extension = $file->guessExtension();
        $service=new Services(); 
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file=$form->get('logo')->getData();
            if(isset($file)){
                $filename=$form->get('libelle')->getData().'.'.$file->getClientOriginalExtension();;
                $file->move("ImageService",$filename);
                $logo='ImageService/'.$filename;
                $service->setLogo($logo);
            }
            $service->setEtat(1);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($service);
            $entityManager ->flush();

            if($form->get('paiement_service')->getData()){
                $url = $appServices->getBpayServerAddress() . '/create/compte/Bpay/' . $service->getId() . '/7';
                $response = $httpClient->request('POST', $url, [
                    'headers' => [
                        'Content-Type: application/json',
                        'Accept' => 'application/json',
                    ]
                ]);
                $content = $response->getContent();
            }

            return $this->redirectToRoute('app_service');
        }
        return $this->render('service/create.html.twig', [
            'controller_name' => 'ServiceController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/service/update/{id}', name: 'service.update')]
    public function update(Request $request,ManagerRegistry $doctrine,$id): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();


        $entityManager = $doctrine->getManager();
        
        $service = $entityManager->getRepository(Services::class)->find($id);

        if($service==null) return $this->redirectToRoute("app_service");

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file=$form->get('logo')->getData();
         
            if(isset($file)){
                $filename=$form->get('libelle')->getData().'.'.$file->getClientOriginalExtension();;
                $file->move("ImageService",$filename);
                $logo='ImageService/'.$filename;
                $service->setLogo($logo);
            }
			

            $entityManager->persist($service);
            $entityManager ->flush();
            return $this->redirectToRoute("app_service");
        }

     
        return $this->render('service/update.html.twig', [
            'controller_name' => 'ServiceController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/service/desactive/{id}', name: 'service.desactive')]
    public function status_invalidate(ManagerRegistry $doctrine,  $id): Response

    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();


        $entityManager = $doctrine->getManager();
        $service = $entityManager->getRepository(Services::class)->find($id);
        $service->setEtat(0);
        $entityManager->flush();
        return $this->redirectToRoute("app_service");
    }

    #[Route('/partenaire/active/{id}', name: 'service.active')]
    public function status_validate(ManagerRegistry $doctrine,  $id): Response

    {  // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();
        
        $entityManager = $doctrine->getManager();
        $service = $entityManager->getRepository(Services::class)->find($id);
        $service->setEtat(1);
        $entityManager->flush();

        return $this->redirectToRoute("app_service");
    }
}