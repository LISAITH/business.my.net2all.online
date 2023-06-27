<?php

namespace App\Controller;

use App\Entity\QuestionDecouvertes;
use App\Entity\ReponseDecouvertes;
use App\Form\QuestionType;
use App\Form\ReponseType;
use App\Repository\QuestionDecouvertesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuestionController extends AuthController
{
    protected $type =4;

    /**
     * @Route("/questions/decouvertes", name="questions.index")
     */
    public function index(QuestionDecouvertesRepository $questionPepository): Response
    {   // verifier si l'utilisateur est un partenaire
        
       
        if(!$this->checkAuthType()) return $this->forbidden();


        $questions = $questionPepository->findAll();
        return $this->render('question/index.html.twig', [
            'questions' => $questions
        ]);
    }

    /**
     * @Route("/questions/decouvertes/update/{id}", name="questions.update")
     * @Route("/questions/decouvertes/create", name="questions.create")
     */
    public function create(QuestionDecouvertes $question = null, Request $request, EntityManagerInterface $entityManager, $id = null): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        if (!$question && $id != null) {
            $question = $entityManager->getRepository(QuestionDecouvertes::class)->find($id);
        } else {
            $question = new QuestionDecouvertes();
            $question->setStatus(true);
            
        }
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            

            $entityManager->persist($question);
            $entityManager->flush();
            if($id) $this->addFlash("flash_success","La question a été modifiée avec succès");
            else    $this->addFlash("flash_success","La question a été ajoutée avec succès");
            

            return $this->redirectToRoute("questions.index");
        }
        return $this->render('question/create.html.twig', [
            'form' => $form->createView(),
            'question' => $question->getId(),
        ]);
    }
    /**
     * @Route("/questions/decouvertes/{id}/reponse/{reponse_id}", name="questions.reponse.update")
     * @Route("/questions/decouvertes/{id}/reponse", name="questions.reponse.create")
     */
    public function value(Request $request, EntityManagerInterface $entityManager, $id ,$reponse_id=null): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();
        $question = $entityManager->getRepository(QuestionDecouvertes::class)->find($id);
        if (!$question ) {
            $this->addFlash("flash_error","La question  n'existe pas");

            return $this->redirectToRoute("questions.index");
        }
        
        if ($reponse_id ) {
            $reponse = $entityManager->getRepository(ReponseDecouvertes::class)->find($reponse_id);
           if(!$reponse) {
                $this->addFlash("flash_error","Echec de la modification de la reponse");
                return $this->redirectToRoute("questions.index");
            }
        } else {
            $reponse = new ReponseDecouvertes();
            $reponse->setQuestion($question); 
            $reponse->setStatus(true);
        }
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            

            $entityManager->persist($reponse);
            $entityManager->flush();
            $this->addFlash("flash_success","Cette réponse a été enregistrée  avec succès");
            return $this->redirectToRoute("questions.index");
        }
        return $this->render('question/value_create.html.twig', [
            'form' => $form->createView(),
            'reponse' => $reponse->getId(),
        ]);
    }



    /**
     * @Route("/questions/decouvertes/{id}/reponse/{reponse_id}/{status}", name="questions.reponse.status")
     */
    public function value_status(Request $request, EntityManagerInterface $entityManager, $id ,$reponse_id, $status): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();
        $question = $entityManager->getRepository(QuestionDecouvertes::class)->find($id);
        if (!$question ) {
            $this->addFlash("flash_error","La question  n'existe pas");

            return $this->redirectToRoute("questions.index");
        }
        
    
        $reponse = $entityManager->getRepository(ReponseDecouvertes::class)->find($reponse_id);
        if(!$reponse) {
            $this->addFlash("flash_error","Echec de la modification de la réponse");
            return $this->redirectToRoute("questions.index");
        }
       


        if ($status==1 ) {
            $this->addFlash("flash_success","La réponse a été activéé avec succès");
            $reponse->setStatus(true);
          
        } elseif($status==0) {
            $this->addFlash("flash_success","La réponse a été désactivéé avec succès");
            $reponse->setStatus(false);
        }else{
            $this->addFlash("flash_error","Echec de la modification de l'état");
        }
        $entityManager->flush();
        return $this->redirectToRoute("questions.index");
    }



    /**
     * @Route("/questions/decouvertes/{id}/{status}", name="questions.status")
     */
    public function status(Request $request, EntityManagerInterface $entityManager, $id ,$status): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();
        $question = $entityManager->getRepository(QuestionDecouvertes::class)->find($id);

        if (!$question ) {
            $this->addFlash("flash_error","La question  n'existe pas");

            return $this->redirectToRoute("questions.index");
        }
        
        if ($status==1 ) {
            $this->addFlash("flash_success","La question a été activéé avec succès");
            $question->setStatus(true);
          
        } elseif($status==0) {
            $this->addFlash("flash_success","La question a été désactivéé avec succès");
            $question->setStatus(false);
        }else{
            $this->addFlash("flash_error","Echec de la modification de l'état");
        }
        $entityManager->flush();
        return $this->redirectToRoute("questions.index");
        
    }
}
