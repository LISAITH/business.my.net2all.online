<?php

namespace App\Controller;

use App\Form\ProfileUpdateType;
use App\Entity\ProfilProspections;
use App\Form\AdminProspectionType;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ParticuliersRepository;
use App\Entity\ParticulierProfilProspections;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProfilProspectionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminProspectionController extends AuthController
{
    protected $type=4;
    #[Route('/admin/prospections', name: 'admin.prospections.index')]
    public function index(Request $request,ParticuliersRepository $particuliersrepository,ProfilProspectionsRepository $profilProspectionsRepository): Response
    {
        if(!$this->checkAuthType()) return $this->forbidden();

        $particuliers=$particuliersrepository->findByProspectAdmin();
        $profilProspection = $profilProspectionsRepository->find(5)->getLibelle();
        return $this->render('admin_prospection/index.html.twig', [
            'controller_name' => 'Business | Les '.$profilProspection,
            'particuliers'=> $particuliers,
            'profil'=>$profilProspection
        ]);
    }

    #[Route('/admin/prospections/profile/{id}', name: 'admin.prospections.profile.update')]
    #[Route('/admin/prospections/profile', name: 'admin.prospections.profile')]
    public function profile(
        Request $request,
        ParticuliersRepository $particuliersrepository,
        ProfilProspectionsRepository $profilProspectionsRepository,
        ManagerRegistry $doctrine,
        $id=null
        ): Response
    {
        if(!$this->checkAuthType()) return $this->forbidden();


        $entityManager = $doctrine->getManager();
        
       
        if ($request->getMethod()=="POST" && $id!=null) {

            $libelle=$request->request->get("libelle");
            $checkLibelle =$profilProspectionsRepository->findOneBy(["libelle"=>$libelle]);
            $update = $profilProspectionsRepository->find($id);
            if(!$update){ 
                $this->addFlash("flash_error","Le profil est introuvable");
                return $this->redirectToRoute('admin.prospections.profile');
            }
            if( null!= $checkLibelle &&  $update!= $checkLibelle  ){ 
                $this->addFlash("flash_error","Ce libellé existe  déjà ! ");
                return $this->redirectToRoute('admin.prospections.profile');
            }
            $update->setLibelle($libelle);
            $entityManager->flush();
            $this->addFlash("flash_success","Le profil a été éffectué avec succès");

            return $this->redirectToRoute('admin.prospections.profile');

        } 

        $profiles = $profilProspectionsRepository->findAll();
        $profilProspection = $profilProspectionsRepository->find(5)->getLibelle();


        return $this->render('admin_prospection/profile.html.twig', [
            'controller_name' => 'Business | Les profiles de prospection',
            'profiles'=> $profiles,
            'profil'=>$profilProspection
        ]);
    }

    #[Route('/admin/prospections/create', name: 'admin.prospections.create')]
    public function create(Request $request,ManagerRegistry $doctrine,ProfilProspectionsRepository $profilProspectionsRepository): Response
    {

        if(!$this->checkAuthType()) return $this->forbidden();
        
        $profilProspection = $profilProspectionsRepository->find(5)->getLibelle();
        $entityManager = $doctrine->getManager();
        $profil = new ParticulierProfilProspections();
        $form = $this->createForm(AdminProspectionType::class,$profil);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $profil->setProfilProspection($profilProspectionsRepository->find(5));
            $entityManager->persist($profil);
            $entityManager->flush();
            $profil->setParentParent($profil->getParticulier()->getId());
            $entityManager->flush();
            
            $this->addFlash("flash_success","L'ajout du ".  $profilProspection ." a été éffectué avec succès");

            return $this->redirectToRoute('admin.prospections.index');


        } 
       
        return $this->render('admin_prospection/create.html.twig', [
            'form' => $form->createView(),
            'controller_name' => "Business | Ajouter un " . $profilProspection  ,
            'profil'=>$profilProspection
        ]);
    }
}
