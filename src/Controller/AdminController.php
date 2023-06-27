<?php

namespace App\Controller;

use App\Entity\Enseignes;
use App\Entity\Abonnement;
use App\Entity\Entreprises;
use App\Entity\Particuliers;
use App\Controller\AuthController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AuthController
{
    protected $type=4;
    #[Route('/admin', name: 'app_admin')]
    public function index(ManagerRegistry $doctrine): Response
    {
        // verifier si l'utilisateur est un partenaire
        if(!$this->checkAuthType()) return $this->forbidden();
        $entityManager = $doctrine->getManager();
        $tab=[];
        $tab["Entreprises"]=  $entityManager-> getRepository(Entreprises::class)->findAll()->count();
        $tab["Enseignes"] = $entityManager-> getRepository(Enseignes::class)->findAll()->count();
        $tab["Particuliers"] = $entityManager-> getRepository(Particuliers::class)->findAll()->count();
        $tab["Abonnements"] = $entityManager-> getRepository(Abonnement::class)->findAll()->count();

        $controller_name ='Business | Dashboard';
        return $this->render('admin/index.html.twig',compact("controller_name","tab"));
    }
}
