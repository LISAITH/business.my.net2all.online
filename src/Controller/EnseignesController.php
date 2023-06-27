<?php

namespace App\Controller;


use App\Entity\Enseignes;
use App\Entity\ApiServices;
use App\Entity\ApiCommunautes;
use App\Form\EnseignesType;
use App\Repository\UserRepository;
use App\Repository\EnseignesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EntreprisesRepository;
use Doctrine\Persistence\ManagerRegistry;
use ApiPlatform\Core\Serializer\JsonEncoder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Encoder\JsonEncoder as EncoderJsonEncoder;

class EnseignesController extends AuthController
{
    protected $type=4;

    #[Route('/enseignes', name: 'app_enseignes')]
    public function index(EnseignesRepository $enseigne): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();  
        $enseignes=$enseigne->findAll();
        return $this->render('enseignes/index.html.twig', [
            'enseignes' => $enseignes,
        ]);
    }


    /**
     * @Route("/enseignes/desactivate/{id}", name="enseigne.desactive")
     * @Route("/enseignes/validate/{id}", name="enseigne.active")
     */
    public function status_validate(EntityManagerInterface $entityManager,EnseignesRepository $repository,  $id): Response

    {  // verifier si l'utilisateur est un partenaire
		
        if(!$this->checkAuthType()) return $this->forbidden();

        $enseigne =$repository->find($id);
        $enseigne->setIsValidated($enseigne->isIsValidated() ? false : true);
		$code = $this->getRamdomText1(4);
        $enseigne->setCodeEnseigne($code);
        $entityManager->persist($enseigne);
        $entityManager->flush();
        return $this->redirectToRoute("app_enseignes");
    }
	
	
	
	
    /**
     * @Route("/enseignes/delete/{id}", name="enseigne_delete")
     */
    public function enseigne_delete(EntityManagerInterface $entityManager,EnseignesRepository $repository,  $id): Response

    {  // verifier si l'utilisateur est un partenaire
		
        if(!$this->checkAuthType()) return $this->forbidden();

      
		
		$ema = $this->getDoctrine()->getManager();
		
	/*	$sql = " SELECT * FROM `enseignes` WHERE id = $id  ";
		
        $enseignet = $this->executeSql($ema, $sql);
		
		$nom_enseigne = $enseignet[0]["nom_enseigne"] ; */
	
		
		
		$sql = " DELETE FROM `api_services` WHERE id_enseigne = $id  ";
		
        $api_service = $this->executeSql($ema, $sql);
		
		
		
		$sql = " DELETE FROM `join_enseigne` WHERE enseigne_id = $id  ";
		
        $join_enseigne = $this->executeSql($ema, $sql);
		
		
		$sql = " SELECT * FROM `prospections` WHERE enseigne_id = $id  ";
		
        $prospect = $this->executeSql($ema, $sql);
		
		foreach($prospect as $pros) {
			
			$pros = $pros["id"] ;
			
			$sql = " DELETE  FROM `decouvertes` WHERE prospection_id = $pros  ";
		
           $prospect = $this->executeSql($ema, $sql);
		
		
		}
		
		
		$sql = " DELETE FROM `prospections` WHERE enseigne_id = $id  ";
		
        $join_enseigne = $this->executeSql($ema, $sql);
		
		
		
	    $sql = " DELETE FROM `join_enseigne_communaute` WHERE enseigne_id = $id  ";
		
        $join_enseigne = $this->executeSql($ema, $sql);
		
				
		
	    $sql = " DELETE FROM `activation_abonnements` WHERE enseigne_id = $id  ";
		
        $join_enseigne = $this->executeSql($ema, $sql);
		
		
		
	/*	$sql = " SELECT * FROM `compte_ecash` WHERE entreprise_id = $id  ";
		
        $entreprise_ecash = $this->executeSql($ema, $sql);
		
		foreach ($entreprise_ecash as $ec) {
			
		$ec = $ec["id"];
			
		$sql = " DELETE FROM `sous_compte` WHERE compte_ecash_id = $ec  ";
		
        $entreprise = $this->executeSql($ema, $sql);
		
		
		
		}
		
		
		$sql = " DELETE FROM `compte_ecash` WHERE entreprise_id = $id  ";
		
        $entreprise = $this->executeSql($ema, $sql); */
		
		
		
		
		$sql = " DELETE FROM `enseignes` WHERE id = $id  ";
		
        $enseigne = $this->executeSql($ema, $sql);
		
		
		/*  $enseigne =$repository->find($id);
		
		  $entityManager->remove($enseigne);
		
          $entityManager->flush(); */
		
		   $this->addFlash('success', 'Enseigne supprimé avec succès');
		
        return $this->redirectToRoute("app_enseignes");
    }
	
	
	
	
	
	

    /**
     * @Route("/enseignes/update/{id}", name="enseignes.update")
     * @Route("/enseignes/create", name="add_enseignes")
     */
    public function create(Enseignes $enseignes = null, Request $request, EntityManagerInterface $entityManager,EntreprisesRepository $entreprisesrepository,$id =null): Response
    {   // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType(6)) return $this->forbidden();
        $entre=$this->getUser()->getEntreprises();
        $id_entreprise=null;
        foreach($entre as $value) {
            $id_entreprise= $value->getId();
        }
        $entreprise=$entreprisesrepository->find($id_entreprise);
        if (!$enseignes && $id != null) {
            $enseignes = $entityManager->getRepository(Enseignes::class)->find($id);
        } else {
            $enseignes = new Enseignes();
            $enseignes->setStatus(false);
        }
        $form = $this->createForm(EnseignesType::class, $enseignes);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $enseignes->setEntreprise($entreprise);
            $enseignes->setUrlImage("default.jpg");

            $entityManager->persist($enseignes);
            $entityManager->flush();
            $this->addFlash('success', 'Enseignes  créer avec succès');
            return $this->redirectToRoute("add_enseignes");
        }
        return $this->render('enseignes/create.html.twig', [
            'form' => $form->createView(),
            'editEnseignes' => $enseignes->getId(),
        ]);
    }

    #[Route('/api/enseignes/entreprise/{id}', name: 'post_enseignes')]
    public function index2($id, SerializerInterface $serializer, EnseignesRepository $ensRepo, EntreprisesRepository $entreRepo): Response
    {
        $data = $serializer->serialize($ensRepo->findBy(["entreprise"=>$entreRepo->find($id)]), EncoderJsonEncoder::FORMAT);

        return new JsonResponse(
            [
                'data' => json_decode($data),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
    }

	#[Route('/api/enseignes/validate/{id}', name: 'validate_ens')]
    public function validate($id, ManagerRegistry $doctrine, SerializerInterface $serializer,EnseignesRepository $ensRepo, EntreprisesRepository $entreRepo): Response
    {

        $ens =$ensRepo->find($id);
        $is_valid=$ens->isIsValidated()?: false;
        if(!$is_valid){
            $em=$doctrine->getManager();
            $code = $this->getRamdomText1(4);
            $ens->setCodeEnseigne($code);
            $ens->setIsValidated(true);
            $em->persist($ens);
            $em->flush();
        }


        return new JsonResponse(
            [
                'data' => json_decode($serializer->serialize("200", EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
    } 

    #[Route('/api/active360/{id}', name: 'active-360')]
    public function activated360($id, ManagerRegistry $doctrine, SerializerInterface $serializer,EnseignesRepository $ensRepo, EntreprisesRepository $entreRepo): Response
    {
        $service_will_be_activated_id=[1,2,3,6,7];
		$communaute_will_be_activated_id=[1,2];

        $ens =$ensRepo->find($id);
        $is_valid=$ens->isIs360Installed()?: false;
        if(!$is_valid){
			
            foreach($service_will_be_activated_id as $s_id){
                $api=new ApiServices();
                $api->setIdServices($s_id);
                $api->setIdEnseigne($ens->getId());
                $api->setIdEntreprise($ens->getEntreprise()->getId());
                $api->setIsInstalled(true);
                $api->setInstallationStatus(0);
                $em=$doctrine->getManager();
                $em->persist($api);
                $em->flush();
            }
			
			/*
            $ens->setIs360Installed(true);
            $em->persist($ens);
            $em->flush();
			*/
			
			//communaute
			
			foreach($communaute_will_be_activated_id as $s_id){
                $api=new ApiCommunautes();
                $api->setIdServices($s_id);
                $api->setIdEnseigne($ens->getId());
                $api->setIdEntreprise($ens->getEntreprise()->getId());
                $api->setIsInstalled(true);
                $api->setInstallationStatus(0);
                $em=$doctrine->getManager();
                $em->persist($api);
                $em->flush();
            }

            $ens->setIs360Installed(true);
			$em=$doctrine->getManager();
            $em->persist($ens);
            $em->flush();
        }
		
		
		


        return new JsonResponse(
            [
                'data' => json_decode($serializer->serialize("200", EncoderJsonEncoder::FORMAT)),
            ],
            Response::HTTP_OK,
            array_merge(['Content-Type' => 'application/json;charset=UTF-8'])
        );
    }

    public function getRamdomText1($n) {
        $characters = 'AZERTYUIOPQSDFGHJKLMWXCVBN';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
	
	
	
	public static function executeSql($entityManager, String $sql) {
        $conn = $entityManager->getConnection();
        $stmt = $conn->executeQuery($sql);
        return $stmt->fetchAllAssociative();
    }


}
