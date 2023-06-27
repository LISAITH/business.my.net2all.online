<?php

namespace App\Controller;

use DateTime;
use App\Entity\Type;
use App\Entity\User;
use App\Entity\Enseignes;
use App\Entity\SousCompte;
use App\Form\EnseigneType;
use App\Entity\CompteEcash;
use App\Entity\Decouvertes;
use App\Entity\Entreprises;
use App\Entity\Distributeur;
use App\Entity\Prospections;
use App\Controller\AuthController;
use App\Entity\ReponseDecouvertes;
use App\Entity\ActivationAbonnements;
use App\Repository\ServicesRepository;
use App\Repository\AbonnementRepository;
use App\Repository\DecouvertesRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ParticuliersRepository;
use App\Repository\UserRepository;
use App\Repository\ProspectionsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ValueProspectionsRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\QuestionDecouvertesRepository;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProspectionController extends AuthController
{   
    protected $type =1;
        
    
    #[Route('/prospections', name: 'prospections.index')]
    public function index(
        Request $request,
        ProspectionsRepository $prospectionsRepository, 
        QuestionDecouvertesRepository $questionRepository ,
        ValueProspectionsRepository $valueRepository
        ): Response
    {   
        if(!$this->checkAuthType()) return $this->forbidden();
        $parent = $this->getUser()->getParticuliers()[0];
        $userProfil =$parent->getProfilId();
        if(!$userProfil ) return $this->forbidden();
        
        $prospections =$prospectionsRepository->findByParentTree($parent->getId());
        $question = $questionRepository->findOneBy(["libelle"=>"potentiel"]);
       
       
        return $this->render('prospection/index.html.twig', [
            'controller_name' => 'Tous mes prospections',
            "prospections"=>$prospections,
            "question" =>$question,
            
          
        ]);
    }


     
    #[Route('/prospections/particulier/{id}', name: 'prospections.particulier')]
    public function prospections(
        Request $request,
        ProspectionsRepository $prospectionsRepository, 
        QuestionDecouvertesRepository $questionRepository ,
        ValueProspectionsRepository $valueRepository,
        ParticuliersRepository $particuliersRepository,
        $id 
      
        ): Response
    {   
        if(!$this->checkAuthType()) return $this->forbidden();
        $parent = $this->getUser()->getParticuliers()[0];
        $userProfil =$parent->getProfilId();
        if(!$userProfil ) return $this->forbidden();

        $particulier= $particuliersRepository->find($id);
        
        $prospectionsByDay = $prospectionsRepository->findByPaginatedByDay($id);
        
        $prosPerDayNow = $prospectionsRepository->findByOneDay($id);
        $prosPerDay = $valueRepository->valueBy("pros/j");

        return $this->render('prospection/my_index.html.twig', [
            'controller_name' => 'Tous ses prospections',
            "prospectionsByDay"=>$prospectionsByDay,
            "prosPerDayNow" =>$prosPerDayNow,
            "prosPerDay" => $prosPerDay,
            "particulier" => $particulier
        ]);
    }


      
    #[Route('/prospections/statistiques', name: 'prospections.statistiques')]
    public function statistique(
        Request $request,
        ProspectionsRepository $prospectionsRepository, 
        QuestionDecouvertesRepository $questionRepository ,
        ValueProspectionsRepository $valueRepository
        ): Response
    {   
        if(!$this->checkAuthType()) return $this->forbidden();
        $parent = $this->getUser()->getParticuliers()[0];
        $userProfil =$parent->getProfilId();
        if(!$userProfil ) return $this->forbidden();
        
        $prosPerDayNow = $prospectionsRepository->findByOneDay($parent->getId());
        $prosPerDay = $valueRepository->valueBy("pros/j");
        $prosPerWeekNow = $prospectionsRepository->findByOneWeek($parent->getId());
        $prosPerWeek = $valueRepository->valueBy("pros/j")*7;

        $prosPerMounthNow = $prospectionsRepository->findByOneMounth($parent->getId());
        $prosPerMounth = $valueRepository->valueBy("pros/j")* $prospectionsRepository->getDays(date("Y-m-d"));;
      
       
     
        return $this->render('prospection/statistic.html.twig', [
            'controller_name' => 'Statistiques de prospection',
           
            "prosPerDayNow" =>$prosPerDayNow,
            "prosPerDay" => $prosPerDay,
            "prosPerWeekNow" =>$prosPerWeekNow,
            "prosPerWeek" => $prosPerWeek,
            "prosPerMounthNow" =>$prosPerMounthNow,
            "prosPerMounth" => $prosPerMounth,
          
        ]);
    }


    #[Route('/prospections/become/client{id}', name: 'prospections.client')]
    public function become_client(
        Request $request,
        ProspectionsRepository $prospectionsRepository,
         ManagerRegistry $doctrine,
         AbonnementRepository $abonnementRepository
         ,$id ): Response
    {
        if(!$this->checkAuthType()) return $this->forbidden();
        $parent = $this->getUser()->getParticuliers()[0];
        $userProfil =$parent->getProfilId();
        if(!$userProfil ) return $this->forbidden();

        $entityManager = $doctrine->getManager();
        $kit =$abonnementRepository->findOneKitAvalable();
        if(!$kit){ 
            $this->addFlash("flash_error","Il n'y a plus de kit disponible");
            return $this->redirectToRoute("prospections.index");
        }

        $prospection = $prospectionsRepository->find($id);
        if(!$prospection){ 
            $this->addFlash("flash_error","Ce prospect est introuvable");
            return $this->redirectToRoute("prospections.index");
        }

        $activationAbonnements = new ActivationAbonnements();
        $activationAbonnements->setAbonnement($kit);
        $activationAbonnements->setEnseigne($prospection->getEnseigne());
        $entityManager->persist($activationAbonnements);
        $entityManager->flush();

        $this->addFlash("flash_success","Le prospect a été  comverti en client avec succès");
        return $this->redirectToRoute("prospections.index");
    }

	
	
	
	#[Route('/prospections/change/pass{id}/{ne}', name: 'prospections.chp')]
    public function chang_p(
       Request $request,ManagerRegistry $doctrine,    UserRepository $usersRepository, 
 ServicesRepository $servicesRepository,UserPasswordHasherInterface $userPasswordHasher
         ,$id, $ne ): Response
    {
        if(!$this->checkAuthType()) return $this->forbidden();
        $parent = $this->getUser()->getParticuliers()[0];
        $userProfil =$parent->getProfilId();
		
        if(!$userProfil ) return $this->forbidden();

        $entityManager = $doctrine->getManager();
		     
		$password = $ne ;
		
        $us = $usersRepository->find($id);
        if(!$us){ 
            $this->addFlash("flash_error","Ce compte est introuvable");
            return $this->redirectToRoute("prospections.index");
        }

        $us->setPassword($userPasswordHasher->hashPassword($us,$password));
        $entityManager->persist($us);
        $entityManager->flush();

        $this->addFlash("flash_success","Mot de passe généré avec succes");
        return $this->redirectToRoute("prospections.index");
    }

	
	
	


    #[Route('/prospections/create', name: 'prospections.create')]
    public function create(Request $request,ManagerRegistry $doctrine,ServicesRepository $servicesRepository,UserPasswordHasherInterface $userPasswordHasher): Response
    {   
        if(!$this->checkAuthType()) return $this->forbidden();
        $parent = $this->getUser()->getParticuliers()[0];
        $userProfil =$parent->getProfilId();
        if(!$userProfil ) return $this->forbidden();
   
        /// Generate new accountNumber for user's CompteEcash
        $accountNumber = $this->getRandomText(10);

        /// Create user's CompteEcash
        $ecash = new CompteEcash();
        $ecash->setNumeroCompte($accountNumber);
        $ecash->setSolde(0);

		$gmp = $this->getRamdomText1(4) ;
		
		
        $type = $doctrine->getRepository(Type::class)->find(6);
		
        $password = $gmp ;
		
		$ph = "+22900000000" ;
      
        $user = new User();
        $user
            ->setType($type)
            ->setStatus(true)
            ->setPassword($userPasswordHasher->hashPassword($user,$password));
        $prospection =new Prospections();
        $entreprise = new Entreprises();
        $entreprise
            ->setUser($user)
            ->setUrlImage("default.png");
        $enseigne = new Enseignes();
        $enseigne
            ->setCodeEnseigne($gmp)
            ->setIsValidated(True)
			->setPhone($ph)
            ->setEntreprise($entreprise)
            ->setUrlImage("default.png")
            ->setStatus(true);
        
        
        $form = $this->createForm(EnseigneType::class, $enseigne);
        $form->handleRequest($request);

       

        if ($form->isSubmitted() && $form->isValid()) {

             
           
            
            $entityManager = $doctrine->getManager();
           
            $entityManager->persist($user);
            $entityManager->persist($entreprise);
            $entityManager->persist($enseigne);
            $prospection->setEnseigne($enseigne);
            $entreprise= $enseigne->getEntreprise();
            $prospection->setEntreprise($entreprise);
            $prospection->setUser($entreprise->getUser());
            $prospection->setParticulier($this->getUser()->getParticuliers()[0]);
            $prospection->setStatus(false);
            $prospection->setDoneAt(new DateTime());
            $entityManager->persist($prospection);

            $ecash->setEntreprise($entreprise);
            $entityManager->persist($ecash);
			

            /// Get all services
            $services = $servicesRepository->findAll();
    
            /// Create [SousCompte] for each service
            foreach ($services as $service){
                $sousCompte = new SousCompte();
                $sousCompte->setSolde(0);
                $sousCompte->setCompteEcash($ecash);
                $sousCompte->setService($service);
                $suffix = substr($service->getLibelle(),0,4);
                $sousCompte->setNumeroSousCompte($accountNumber . "-" .$suffix);
    
                /// Save SousCompte to database
                $entityManager->persist($sousCompte);
            }
           
			
            try {
                $email=$entreprise->getUser()->getEmail();
           
                //$mymsg = urlencode($email);
				//$mymsg = urlencode($entreprise->getUser()->getNumTel());
				
				$mymsg =$entreprise->getNumTel();
				
              
				
                //$this->sendSms($entreprise->getNumTel(), $mymsg,$password); 
                $entityManager ->flush();
                $this->addFlash("flash_success","Le prospect a été ajouté avec succès");           
                return $this->redirectToRoute("prospections.index");
            } catch (\Throwable $th) {
				
                $this->addFlash("flash_error","Echec d'envoi de massage ");           
                
            }
           
           
           
        }

     
        
        return $this->render('prospection/create.html.twig', [
            'controller_name' => 'Business | ajouter un prospect ',
            'form' => $form->createView()
        ]);
    }


    #[Route('/prospections/decouvertes/{id}', name: 'prospections.decouvertes.create')]
    public function decouvertes(
        Request $request,
        ManagerRegistry $doctrine,
        ProspectionsRepository $prospectionsRepository,
        QuestionDecouvertesRepository $questionRepository,
        DecouvertesRepository $decouvertesRepository ,$id): Response
    {   
        
        if(!$this->checkAuthType()) return $this->forbidden();
        $parent = $this->getUser()->getParticuliers()[0];
        $userProfil =$parent->getProfilId();
        if(!$userProfil ) return $this->forbidden();

        $prospection =$prospectionsRepository->find($id);
        $questions =$questionRepository->findBy(["status"=>true]);
        if(!$prospection){
            $this->addFlash("flash_error","Erreur ! Prospection introuvable");
            return $this->redirectToRoute("prospections.index"); 
        }
        $preponses = $decouvertesRepository->findArray($id);

        if($request->isMethod("POST")){
            $reponses =$request->request->get("reponse");
            $entityManager = $doctrine->getManager();
         
            if(is_array($reponses)){
                foreach( $reponses as $question_id=>$reponse_id){
                    $decouverte = new Decouvertes();
                   
                    $reponse = $entityManager->getRepository(ReponseDecouvertes::class)->find($reponse_id);
                    if(!$reponse){
                        $this->addFlash("flash_error","Erreur lors de la sauvegarde,réponse introuvable");
                        return $this->redirectToRoute("prospections.index"); 

                    }
                    $decouverte
                        ->setProspection($prospection)
                        ->setQuestion($reponse->getQuestion())
                        ->setReponse($reponse);
                    $entityManager->persist($decouverte);    
                }
                $entityManager->flush();
                $this->addFlash("flash_success","La fiche de decouverte a été sauvegardée avec succès");
                return $this->redirectToRoute("prospections.index");    
            }
        }

        
        return $this->render('prospection/decouvertes.html.twig', [
            'controller_name' => 'Tous mes prospections',
            "prospection"=>$prospection,
            "questions"=> $questions,
            "preponses"=>$preponses
        ]);
    }


    #[Route('/prospections/potentiels/{id}', name: 'prospections.potentiels.create')]
    public function potentiel(
        Request $request,
        ManagerRegistry $doctrine,
        ProspectionsRepository $prospectionsRepository,
        QuestionDecouvertesRepository $questionRepository,
        DecouvertesRepository $decouvertesRepository ,$id): Response
    {   
        
        if(!$this->checkAuthType()) return $this->forbidden();
        $parent = $this->getUser()->getParticuliers()[0];
        $userProfil =$parent->getProfilId();
        if(!$userProfil ) return $this->forbidden();

        $prospection =$prospectionsRepository->find($id);
        $questions =$questionRepository->findBy(["status"=>true]);
        if(!$prospection){
            $this->addFlash("flash_error","Erreur ! Prospection introuvable");
            return $this->redirectToRoute("prospections.index"); 
        }
        $preponses = $decouvertesRepository->findArray($id);

        if($request->isMethod("POST")){
            $reponse_id =$request->request->get("reponse".$id);
            $entityManager = $doctrine->getManager();
         
            if($reponse_id){
                
                
      
                $reponse = $entityManager->getRepository(ReponseDecouvertes::class)->find($reponse_id);
                if(!$reponse){
                        $this->addFlash("flash_error","Erreur lors de la sauvegarde,réponse introuvable");
                        return $this->redirectToRoute("prospections.index"); 

                }
                $prospection->setPotentiel($reponse);
                $entityManager->flush();
                $this->addFlash("flash_success","Le potentiel a été sauvegardé avec succès");
                return $this->redirectToRoute("prospections.index");    
            }
        }

        
        return $this->redirectToRoute("prospections.index"); 
    }



    #[Route('/prospections/validated/{id}', name: 'prospections.validated')]
    public function validated(
        Request $request,
        ProspectionsRepository $prospectionsRepository,
         ManagerRegistry $doctrine,
         AbonnementRepository $abonnementRepository
         ,$id ): Response
    {

         
        if(!$this->checkAuthType()) return $this->forbidden();
        $parent = $this->getUser()->getParticuliers()[0];
        $userProfil =$parent->getProfilId();
        if(!$userProfil ) return $this->forbidden();

        if(!$request->isMethod("POST")) return $this->redirectToRoute("prospections.particulier",["id"=>$id]);
        $date =$request->request->get("date");
       

     
        $entityManager = $doctrine->getManager();
        $prospections = $prospectionsRepository->findByDayProsToValid($id,$date);
       
        if(!$prospections){ 
            $this->addFlash("flash_error","pas prospection disponible");
            return $this->redirectToRoute("prospections.particulier",["id"=>$id]);
        }

        foreach($prospections as $prospection){
            $prospection->setStatus(true);
            $prospection->setValidatedAt(new DateTime());
            $prospection->setValidator($parent);
        };
        $entityManager->flush();
        $this->addFlash("flash_success",count($prospections)." prospection(s) prospections validée(s)");
        return $this->redirectToRoute("prospections.particulier",["id"=>$id]);
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

    protected function getRandomText($n): string
    {
        $characters = '0123456789ABCDEFGHILKMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
	
	
	 protected function getRandompassText($text, $n): string
    {
        $characters = $text;
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    


}
