<?php

namespace App\Controller;

use App\Entity\Formule;
use App\Entity\Services;
use App\Form\FormuleType;
use App\Form\FormuleServiceType;
use App\Controller\AuthController;
use App\Repository\FormuleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class FormuleController extends AuthController
{
 

    #[Route('/formules', name: 'formules.index')]
    public function index(FormuleRepository $formuleRepository): Response
    {
        $formules=$formuleRepository->findAll();
        return $this->render('formule/index.html.twig', [
            'controller_name' => 'Business |Listes des  formules',
            'formules'=>$formules,
        ]);
    }




    #[Route('/formules/update/{id}', name: 'formules.update')]
    #[Route('/formules/create', name: 'formules.create')]
    public function create(Formule $formule=null,Request $request,ManagerRegistry $doctrine, $id=null): Response
    {
        $entityManager = $doctrine->getManager();
        
        $add = true;
        if(!$formule && $id!=null){

            $formule = $entityManager->getRepository(Formule::class)->find($id);
            $controller_name="Business | Modifier une formule"; 
            $add = false;
            
        }else{
            $formule=new Formule();
            $formule->setStatus(True);
            $controller_name="Business | Ajouter une formule";
           
        }
        $form = $this->createForm(FormuleType::class, $formule);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if($add)  $this -> addFlash("formule_edit","La formule  a été ajoutée avec succès");
            else $this -> addFlash("formule_edit","La formule  a été modifiée avec succès");
            $entityManager = $doctrine->getManager();
            $entityManager->persist($formule);
            $entityManager ->flush();
            return $this->redirectToRoute('formules.index');

        }
        return $this->render('formule/create.html.twig', [
            'form' => $form->createView(),
            'controller_name' => $controller_name,
            'formule' =>$formule->getId(),
        ]);
    }


  
    #[Route('/formules/service/add', name: 'formules.service.add')]
    public function service_add(Request $request,ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $form = $this->createForm(FormuleServiceType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $formule = $form->getData()["formule"];
            $service =$form->getData()["service"];
            $formule->addService($service);

            $entityManager->flush();
            
            $this->addFlash("service_add","L'ajout du service à la formule  a été éffectué avec succès");

            return $this->redirectToRoute('formules.index');

        } 
        return $this->render('formule/add_service_formule.html.twig', [
            'form' => $form->createView(),
            'controller_name' => "Business | Ajouter service au formule"
        ]);
    }



    #[Route('/formules/service/remove/{id}', name: 'formules.service.remove',methods:["post"])]
    public function service_remove(Request $request,ManagerRegistry $doctrine,$id ): Response
    {   
        $entityManager = $doctrine->getManager();
        $formule = $entityManager->getRepository(Formule::class)->find($id);
        $services_id = $request->request->all();


        // verifie si la formule existe
        if($formule==null) {
            $this->addFlash("service_add","Echec du retraitn du service de la formule ( la formule est introuvable )");
            return $this->redirectToRoute('formules.index');
        }
        // verifie si la formule existe
        if($services_id==null) {
            $this->addFlash("service_add","Echec du retraitn du service de la formule ( Aucun service choisi )");
            return $this->redirectToRoute('formules.index');
        }


        $services_get = $formule->getServices();
        $services=[];


        // verifie si tous les services à supprimer existent pour cette formule
        foreach($services_id as $service_id){
            $service = $entityManager->getRepository(Services::class)->find($service_id);
            if(! $services_get->contains($service)){
                $this->addFlash("service_add","Echec du retrait du service de la formule ( service est introuvable )");
                return $this->redirectToRoute('formules.index');
            }else{
                $services[]=$service; 
            }
        }


        // retrait des services de la formule
        foreach($services as $service ){
            $formule->removeService($service);
        }


        $entityManager->flush();
        $this->addFlash("service_add","Retrait de (". count($services).") service(s) de la formule \"".$formule->getNomFormule()."\"");


        return $this->redirectToRoute('formules.index');
    }





    #[Route('/formules/status/{id}/unvalidate', name: 'formules.status.unvalidate')]
    #[Route('/formules/status/{id}/validate', name: 'formules.status.validate')]
    public function status_validate(ManagerRegistry $doctrine,FormuleRepository $formuleRepository,  $id): Response

    {  
        $entityManager = $doctrine->getManager();
        $formule =  $formuleRepository->find($id);
        if($formule != null){
            $formule->setStatus($formule->isStatus() ? false : true);
            $entityManager->flush();
        }else{
            
            $this -> addFlash("formule_errors","Echec de l'opération");
        }
        
        if($formule->isStatus()) $this -> addFlash("formule_status","La formule \"" . $formule->getNomFormule()  ."\" a été activée avec succès");
        else $this -> addFlash("formule_status","La formule \"" . $formule->getNomFormule() ."\" a été désactivée avec succès") ;
        
        

        return $this->redirectToRoute("formules.index");
    }



}
