<?php

namespace App\Controller;

use App\Entity\Enseignes;
use App\Entity\Abonnement;
use App\Entity\Entreprises;
use App\Entity\Particuliers;
use App\Controller\AuthController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AuthController
{
    protected $type=4;

    
    #[Route('/', name: 'app_home')]
    public function index(ManagerRegistry $doctrine): Response
    {
        
        // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuth()) return $this->forbidden();
        $usertype =$this->getUser()->getType()->getId();
      
        switch ($usertype) {
            case 4:
                return $this->redirectToRoute("app_dashboard");
                break;
            case 3:
                return $this->redirectToRoute("distributeurs.index");
                break;
            case 2:
                return $this->redirectToRoute("point_ventes.index");
                break;
            case 5:
                return $this->redirectToRoute("ventes.index");
                    break;
           
            
            default:
                # code...
                break;
        }

        $entityManager = $doctrine->getManager();
        $tab=[];
        $tab["Entreprises"]= count( $entityManager-> getRepository(Entreprises::class)->findAll());
        $tab["Enseignes"] = count ($entityManager-> getRepository(Enseignes::class)->findAll());
        $tab["Particuliers"] = count ($entityManager-> getRepository(Particuliers::class)->findAll());
        $tab["Abonnements"] = count($entityManager-> getRepository(Abonnement::class)->findAll());
       
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            "tab"=>$tab
        ]);
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function Dashboard(ManagerRegistry $doctrine): Response
    {   // verifier si l'utilisateur est un partenaire
        
        if(!$this->checkAuthType()) return $this->forbidden();

        $entityManager = $doctrine->getManager();
        $tab=[];
        $tab["Entreprises"]= count( $entityManager-> getRepository(Entreprises::class)->findAll());
        $tab["Enseignes"] = count ($entityManager-> getRepository(Enseignes::class)->findAll());
        $tab["Particuliers"] = count ($entityManager-> getRepository(Particuliers::class)->findAll());
        $tab["Abonnements"] = count($entityManager-> getRepository(Abonnement::class)->findAll());

        $controller_name ='Business | Dashboard';
        return $this->render('home/dashboard.html.twig',compact("controller_name","tab"));
     
    }
}
