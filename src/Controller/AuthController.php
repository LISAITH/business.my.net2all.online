<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class AuthController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;


    protected $type;
    

    public function __construct(Security $security)
    {
       $this->security = $security;
      
    }

    public function getUser():User{
        
        return $this->security->getUser();
    }
    
    public function checkAuth(){
        return $this->security->getUser()!=null;
    }

    public function checkAuthType($id=null){
        if(!$this->checkAuth()) return false;
        if($id){
            return $this->security->getUser()->getType()->getId()===$id;
            }
        return $this->security->getUser()->getType()->getId()===$this->type;
    }

    public function forbidden(){
        return $this->redirectToRoute("app_home");
    }

    public function sendSms($phone,$login,$password){
        $title = "Net2All";
      
        //$msg = "Bienvenu+a+NET2ALL.+Connecter+vous+à+votre+panel+unique+à+adresse+suivante+:+https://dev.n2a.online/business/public/index.php/login.+Login:+($email)+mdp:+($email)";
        $msg = "Bienvenu+a+NET2ALL.+Acceder+a+Votre+panel+https://my.net2all.online/public/.+Login:+".$login."+mdp:+$password";
        // $msg = wordwrap($msg, 70);
        
        $sms = file_get_contents("http://130.185.251.88/api/http/sendmsg.php?user=magmatel2&password=SMSmagamatelys2@2017&from=$title&to=%2B$phone&text=$msg&api=14265");
     
    }
   
}