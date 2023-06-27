<?php

namespace App\Controller;

use App\Entity\Communaute;
use App\Form\CommunauteType;
use App\Controller\AuthController;
use Doctrine\Persistence\ObjectManager;
use App\Repository\CommunauteRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommunauteController extends AuthController
{   protected $type=4;
    #[Route('/communaute', name: 'app_communaute')]
    public function index(CommunauteRepository $comm): Response
    {
        // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $communaute=$comm->findAll();
        return $this->render('communaute/index.html.twig', [
            'communaute'=>$communaute,
        ]);
    }
    #[Route('/communaute/update/{id}', name: 'communaute.update')]
    #[Route('/addcommunaute', name: 'add_communaute')]
    public function create(Communaute $communaute=null,Request $request,ManagerRegistry $doctrine, $id=null): Response
    {
        // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $entityManager = $doctrine->getManager();
        if(!$communaute && $id!=null){
            $communaute=$entityManager->getRepository(Communaute::class)->find($id);
        }else{
            $communaute=new Communaute();
            $communaute->setEtat(1);
        }
        $form = $this->createForm(CommunauteType::class, $communaute);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file=$form->get('logo')->getData();
            $filename=$form->get('libelle')->getData().'.'.$file->getClientOriginalExtension();;
            $file->move("ImageService",$filename);
            $logo='ImageService/'.$filename;
            $communaute->setLogo($logo);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($communaute);
            $entityManager ->flush();
            $this->addFlash('success', 'Communauté créer avec succès');
            return $this->redirectToRoute('app_communaute');
        }
        return $this->render('communaute/create.html.twig', [
            'form' => $form->createView(),
            'editCommunaute'=>$communaute->getId(),
        ]);
    }


    

    // #[Route('/communaute/update/{id}', name: 'communaute.update')]
    // public function update(Request $request,ManagerRegistry $doctrine,$id): Response
    // {
    //     $entityManager = $doctrine->getManager();
        
    //     $communaute = $entityManager->getRepository(Communaute::class)->find($id);

    //     if($communaute==null) return $this->redirectToRoute("app_communaute");

    //     $form = $this->createForm(CommunauteType::class, $communaute);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->persist($communaute);
    //         $entityManager ->flush();
    //         return $this->redirectToRoute("app_communaute");
    //     }

     
    //     return $this->render('communaute/update.html.twig', [
    //         'form' => $form->createView()
    //     ]);
    // }
}
