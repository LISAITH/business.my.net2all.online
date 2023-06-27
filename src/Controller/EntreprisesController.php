<?php

namespace App\Controller;

use App\Entity\Entreprises;
use App\Form\EntreprisesType;
use App\Controller\AuthController;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EntreprisesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EntreprisesController extends AuthController
{
    protected $type=4;
    #[Route('/entreprises', name: 'app_entreprises')]
    public function index(EntreprisesRepository $entreprisesrepository): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $entreprise=$entreprisesrepository->findAll();

        return $this->render('entreprises/index.html.twig', [
            'entreprise'=>$entreprise,
        ]);
    }


    /**
     * @Route("/entreprises/enseigne/{id}", name="entreprises.enseigne")
     */
    public function entreprise_enseigne(EntreprisesRepository $entreprisesrepository,$id): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();

        $enseignes=$entreprisesrepository->find($id)->getEnseignes();
        // return $this->render('entreprises/entrprise_allenseigne.html.twig', [
        //     'enseignes'=>$enseignes,
        // ]);
        return new JsonResponse([
            'enseignes' => $enseignes,
            'html' => $this->renderView('entreprises/entrprise_allenseigne.html.twig', ['enseignes' => $enseignes,]),
        ]);
    }

    /**
     * @Route("/entreprises/desactivate/{id}", name="entreprises.desactive")
     * @Route("/entreprises/validate/{id}", name="entreprises.active")
     */
    public function status_validate(EntityManagerInterface $entityManager,UserRepository $repository,  $id): Response

    {  // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();
        $user =  $repository->find($id);
        $user->setStatus($user->isStatus() ? false : true);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute("app_entreprises");
    }
	
	
	
	
	 /**
     
     * @Route("/entreprises/delete/{id}", name="entreprise_delete")
     */
    public function delete_entreprise(EntityManagerInterface $entityManager,UserRepository $repository,  $id): Response

    {  // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();
		
		$ema = $this->getDoctrine()->getManager();
		
		
		
		$sql = " SELECT * FROM `enseignes` WHERE entreprise_id = $id  ";
		
        $enseignes = $this->executeSql($ema, $sql);
		
		
		
		$sql = " SELECT * FROM `entreprises` WHERE id = $id  ";
		
        $entreprise = $this->executeSql($ema, $sql);
		
		$entreprise= $entreprise[0] ;
		
		$userId = $entreprise["user_id"];
		
		
	
		
		
		
		
		foreach($enseignes as $en) {
		
		
		$ensid = $en["id"] ;
			
			
	    $sql = " DELETE FROM `api_services` WHERE id_enseigne = $ensid  ";
		
        $api_service = $this->executeSql($ema, $sql);
		
		
		
		$sql = " DELETE FROM `join_enseigne` WHERE enseigne_id = $ensid  ";
		
        $join_enseigne = $this->executeSql($ema, $sql);
		
			$sql = " SELECT * FROM `prospections` WHERE enseigne_id = $ensid  ";
		
        $prospect = $this->executeSql($ema, $sql);
		
		foreach($prospect as $pros) {
			
			$pros = $pros["id"] ;
			
			$sql = " DELETE  FROM `decouvertes` WHERE prospection_id = $pros  ";
		
           $prospect = $this->executeSql($ema, $sql);
		
		
		}
		
		
		$sql = " DELETE FROM `prospections` WHERE enseigne_id = $ensid  ";
		
        $join_enseigne = $this->executeSql($ema, $sql);
		
		
		
	    $sql = " DELETE FROM `join_enseigne_communaute` WHERE enseigne_id = $ensid  ";
		
        $join_enseigne = $this->executeSql($ema, $sql);
		
				
		
	    $sql = " DELETE FROM `activation_abonnements` WHERE enseigne_id = $ensid  ";
		
        $join_enseigne = $this->executeSql($ema, $sql);
		
			
			
		$sql = " DELETE FROM `enseignes` WHERE id = $ensid  ";
		
        $enseigne = $this->executeSql($ema, $sql);
		
			
	
		
		
		}
		
			
		$sql = " DELETE FROM `entreprises` WHERE id = $id  ";
		
        $entreprises = $this->executeSql($ema, $sql);
		
		
		
		
		$sql = " DELETE FROM `api_services` WHERE id_entreprise = $id  ";
		
        $api_service = $this->executeSql($ema, $sql);
		
		
		$sql = " SELECT * FROM `compte_ecash` WHERE entreprise_id = $id  ";
		
        $entreprise_ecash = $this->executeSql($ema, $sql);
		
		foreach ($entreprise_ecash as $ec) {
			
		$ec = $ec["id"];
			
		$sql = " DELETE FROM `sous_compte` WHERE compte_ecash_id = $ec  ";
		
        $entreprise = $this->executeSql($ema, $sql);
		
		
		
		}
		
		
		$sql = " DELETE FROM `compte_ecash` WHERE entreprise_id = $id  ";
		
        $entreprise = $this->executeSql($ema, $sql);
		
		
		
		
		

		
		$sql = " DELETE FROM `user` WHERE id = $userId  ";
		
        $entrepriseUser = $this->executeSql($ema, $sql);
		
		
		
		
		
		
	
		
		  $this->addFlash('success', 'Entreprise supprimé avec succès');
        return $this->redirectToRoute("app_entreprises");
    }
	
	

    #[Route('/entreprise/update/', name: 'entreprise.update')]
    public function updateEntreprise(Request $request,EntityManagerInterface $entityManager,EntreprisesRepository $entreprisesrepository, UserRepository $userRepository): Response
    {
        // verifier si l'utilisateur est un partenaire
         if(!$this->checkAuthType(6)) return $this->forbidden();

        $userId=$this->getUser()->getEntreprises();
        $id_entreprise=null;
        foreach($userId as $value) {
            $id_entreprise= $value->getId();
        }
        $entreprise=$entreprisesrepository->find($id_entreprise);
        $form = $this->createForm(EntreprisesType::class, $entreprise);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $file=$form->get('url_image')->getData();
            if(isset($file)){
                $filename=$form->get('nom_entreprise')->getData().'.'.$file->getClientOriginalExtension();;
                $file->move("ImageService",$filename);
                $logo='ImageService/'.$filename;
                $entreprise->setUrlImage($logo);
            }
            $user=$form->get('user')->getData();
            $user->setNumero($form->get('num_tel')->getData());
            $entreprise->setUser($user);
            $entityManager->persist($entreprise);
            $entityManager ->flush();
            $this->addFlash('success', 'Modification d\'une entreprise avec succès');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('entreprises/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
	
	
		public static function executeSql($entityManager, String $sql) {
        $conn = $entityManager->getConnection();
        $stmt = $conn->executeQuery($sql);
        return $stmt->fetchAllAssociative();
    }
	
	
}
