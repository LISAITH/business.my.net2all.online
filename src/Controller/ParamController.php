<?php

namespace App\Controller;

use App\Entity\ParamProspections;
use App\Entity\ValueProspections;
use App\Form\ParamType;
use App\Form\ValueType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ParamProspectionsRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ParamController extends AuthController
{
    protected $type =4;

    /**
     * @Route("/params/prospections", name="params.index")
     */
    public function index(ParamProspectionsRepository $paramPepository): Response
    {   // verifier si l'utilisateur est un partenaire
        
       
        if(!$this->checkAuthType()) return $this->forbidden();


        $params = $paramPepository->findAll();
        return $this->render('param/index.html.twig', [
            'params' => $params
        ]);
    }

    /**
     * @Route("/params/prospections/update/{id}", name="params.update")
     * @Route("/params/prospections/create", name="params.create")
     */
    public function create(ParamProspections $param = null, Request $request, EntityManagerInterface $entityManager, $id = null): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        if (!$param && $id != null) {
            $param = $entityManager->getRepository(ParamProspections::class)->find($id);
        } else {
            $param = new ParamProspections();
            
        }
        $form = $this->createForm(ParamType::class, $param);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            

            $entityManager->persist($param);
            $entityManager->flush();
            $this->addFlash("flash_success","Le paramètre  a été ajouté avec succès");

            return $this->redirectToRoute("params.index");
        }
        return $this->render('param/create.html.twig', [
            'form' => $form->createView(),
            'param' => $param->getId(),
        ]);
    }
    /**
     * @Route("/params/prospections/{id}/value/{value_id}", name="params.value.update")
     * @Route("/params/prospections/{id}/value", name="params.value.create")
     */
    public function value(Request $request, EntityManagerInterface $entityManager, $id ,$value_id=null): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();
        $param = $entityManager->getRepository(ParamProspections::class)->find($id);
        if (!$param ) {
            $this->addFlash("flash_error","Le paramètre  n'existe pas");

            return $this->redirectToRoute("params.index");
        }
        
        if ($value_id ) {
            $valu = $entityManager->getRepository(ValueProspections::class)->find($value_id);
           if(!$valu) {
                $this->addFlash("flash_error","Echec de la modification de cette valeur");
                return $this->redirectToRoute("params.index");
            }
        } else {
            $valu = new ValueProspections();
            $valu->setParamProspection($param);   
        }
        $form = $this->createForm(ValueType::class, $valu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            

            $entityManager->persist($valu);
            $entityManager->flush();
            $this->addFlash("flash_success","Cette valeur a été enregistrée  avec succès");
            return $this->redirectToRoute("params.index");
        }
        return $this->render('param/value_create.html.twig', [
            'form' => $form->createView(),
            'valu' => $valu->getId(),
        ]);
    }
}
