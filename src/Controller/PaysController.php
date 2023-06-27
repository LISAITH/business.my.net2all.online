<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Form\PaysType;
use App\Controller\AuthController;
use App\Repository\PaysRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaysController extends AuthController
{
    protected $type =4;

    /**
     * @Route("/pays", name="app_pays")
     */
    public function index(PaysRepository $repository): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();


        $pays = $repository->findAll();
        return $this->render('pays/index.html.twig', [
            'pays' => $pays
        ]);
    }

    /**
     * @Route("/pays/update/{id}", name="pays.update")
     * @Route("/pays/create", name="add_pays")
     */
    public function create(Pays $pays = null, Request $request, EntityManagerInterface $entityManager, $id = null): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        if (!$pays && $id != null) {
            $pays = $entityManager->getRepository(Pays::class)->find($id);
        } else {
            $pays = new Pays();
            $pays->setStatus(1);
        }
        $form = $this->createForm(PaysType::class, $pays);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pays->setIndicatif($request->request->get('indicatif'));

            $entityManager->persist($pays);
            $entityManager->flush();

            return $this->redirectToRoute("app_pays");
        }
        return $this->render('pays/create.html.twig', [
            'form' => $form->createView(),
            'editPays' => $pays->getId(),
        ]);
    }



    // Mise à jour effectuée avec succès!

    /**
     * @Route("/pays/desactivate/{id}", name="pays.desactive")
     * @Route("/pays/validate/{id}", name="pays.active")
     */
    public function status_validate(EntityManagerInterface $entityManager, PaysRepository $repository,  $id): Response

    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $pays =  $repository->find($id);
        $pays->setStatus($pays->getStatus() ? 0 : 1);
        $entityManager->persist($pays);
        $entityManager->flush();
        return $this->redirectToRoute("app_pays");
    }
}