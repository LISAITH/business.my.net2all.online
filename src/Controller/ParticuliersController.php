<?php

namespace App\Controller;

use App\Form\ParticuliersType;
use App\Controller\AuthController;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ParticuliersRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ParticuliersController extends  AuthController
{
    protected $type=4;
    #[Route('/particuliers', name: 'app_particuliers')]
    public function index(ParticuliersRepository $particuliersrepository): Response
    {
        if(!$this->checkAuthType()) return $this->forbidden(); 
        $particuliers=$particuliersrepository->findAll();
        return $this->render('particuliers/index.html.twig', [
            'particuliers'=>$particuliers
        ]);
    }

    #[Route('/particuliers/update/', name: 'particuliers.update')]
    public function updateParticulier(Request $request,EntityManagerInterface $entityManager,ParticuliersRepository $particuliersrepository): Response
    {
        // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType(1)) return $this->redirectToRoute('app_home');

        $userId=$this->getUser()->getParticuliers();
        $id_particuliier=null;
        foreach($userId as $value) {
            $id_particuliier= $value->getId();
        }

        $particuliier=$particuliersrepository->find($id_particuliier);
        $form = $this->createForm(ParticuliersType::class, $particuliier);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user=$form->get('user')->getData();
            $user->setNumero($form->get('num_tel')->getData());
            $particuliier->setUser($user);
            $entityManager->persist($particuliier);
            $entityManager ->flush();
            $this->addFlash('success', 'Modification d\'une entreprise avec succès');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('particuliers/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/particuliers/desactivate/{id}", name="particuliers.desactive")
     * @Route("/particuliers/validate/{id}", name="particuliers.active")
     */
    public function status_validate(EntityManagerInterface $entityManager,UserRepository $repository,  $id): Response

    {  // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();
        $user =  $repository->find($id);
        $user->setStatus($user->isStatus() ? false : true);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute("app_particuliers");
    }
	
	
	 /**
     * @Route("/particuliers/delete/{id}", name="particulier_delete")
     */
    public function delete_particulier(EntityManagerInterface $entityManager,UserRepository $repository,  $id): Response

    {  // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();
		
		
			$ema = $this->getDoctrine()->getManager();
		
		
		
		$sql = " SELECT * FROM `compte_ecash` WHERE particulier_id = $id  ";
		
        $part_ecash = $this->executeSql($ema, $sql);
		
		foreach ($part_ecash as $ec) {
			
		$ec = $ec["id"];
			
		$sql = " DELETE FROM `sous_compte` WHERE compte_ecash_id = $ec  ";
		
        $part = $this->executeSql($ema, $sql);
		
		
		
		}
		
		
		$sql = " DELETE FROM `compte_ecash` WHERE particulier_id = $id  ";
		
        $particulier = $this->executeSql($ema, $sql);
		
		
			
		$sql = " SELECT * FROM `particuliers` WHERE id = $id  ";
		
        $particulier = $this->executeSql($ema, $sql);
		
		$particulier = $particulier[0] ;
		
		$userId = $particulier["user_id"];
		
		$sql = " DELETE FROM `particuliers` WHERE id = $id  ";
		
        $particuliers = $this->executeSql($ema, $sql);
		
	
		
		
		
		$sql = " DELETE FROM `user` WHERE id = $userId  ";
		
        $particulierUser = $this->executeSql($ema, $sql);
		
		
		
		
		 $this->addFlash('success', 'Particulier supprimé avec succès');
		
        return $this->redirectToRoute("app_particuliers");
    }
	
	
	
	
  public static function executeSql($entityManager, String $sql) {
        $conn = $entityManager->getConnection();
        $stmt = $conn->executeQuery($sql);
        return $stmt->fetchAllAssociative();
    }
	
}
