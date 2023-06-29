<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Security\AppCustomAuthenticator;
use Mpdf\Tag\I;
use Symfony\Component\Validator\Validation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SecurityController extends AbstractController
{




    #[Route(path: '/api/login', name: 'api_login', methods: ['POST'])]
    public function loginUser(Request $request)
    {

        // if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
        //     return $this->json([
        //         'error' => 'Invalid login request: check that the Content-Type header is "application/json".'
        //     ], 400);
        // }
              
        $user = $this->getUser();
    
        return $this->json([
            "username" => $user->getUserIdentifier(),
            "roles" => $user->getRoles(),
        ]);
    }


    /**
     * @Route("/login", name="app_login")
     */
    public function login(Request $request,UserPasswordHasherInterface $hasher,UserAuthenticatorInterface $userAuthenticator,AppCustomAuthenticator $authenticator,UserRepository $repositoryUser): Response 
    {
        $error = null;
        $user = null;
        
        if ($request->isMethod("POST")) {
        
            if ($this->isCsrfTokenValid('authenticate', $request->request->get('_csrf_token'))) {
                $password =  $request->request->get('password');
                $login =  $request->request->get('login');
               
                if (empty($password)) $error = "Le mot de passe ne doit pas être vide";
                if (empty($login)) $error = "L'identifiant ne doit pas être vide";
                
                if (null == $error) {
                    $user = $repositoryUser->findOneBy(["email" => $login]);
                  
                    if (!$user) $error = "Ce compte n'existe pas";
                   
                    if (  $user &&  $hasher->isPasswordValid($user, $password)) {

                        return $userAuthenticator->authenticateUser(
                            $user,
                            $authenticator,
                            $request
                        );
                    } elseif(!$error) {
                        $error = "Mot de passe incorrect";
                    }
                }
                
            } else {
                $error = "Token expiré, veuillez réessayer.";
            }
        }
       
        return $this->render('security/login.html.twig');
    }


    /**
     * @Route("/login/connect", name="login_connect")
     */
    public function  login_connect(
        Request $request,
        UserPasswordHasherInterface $hasher,
        UserAuthenticatorInterface $userAuthenticator,
        AppCustomAuthenticator $authenticator,
        UserRepository $repositoryUser
    ): Response {
        $error = null;
        $user = null;


        if ($request->isMethod("POST")) {
        
            if ($this->isCsrfTokenValid('authenticate', $request->request->get('_csrf_token'))) {
                $password =  $request->request->get('password');
                $login =  $request->request->get('login');
               
                if (empty($password)) $error = "Le mot de passe ne doit pas être vide";
                if (empty($login)) $error = "L'identifiant ne doit pas être vide";
                
                if (null == $error) {
                    $user = $repositoryUser->findOneBy(["email" => $login]);
                  
                    if (!$user) $error = "Ce compte n'existe pas";
                   
                    if (  $user &&  $hasher->isPasswordValid($user, $password)) {

                        return $userAuthenticator->authenticateUser(
                            $user,
                            $authenticator,
                            $request
                        );

                    } elseif(!$error) {
                        $error = "Mot de passe incorrect";
                    }
                }
               


                // if ($this->getUser()) {
                //    return $this->redirectToRoute('app_home');
                // }
           

            } else {
                $error = "Token expiré, veuillez réessayer.";
            }
        }
       

        $this->addFlash("error", $error);
        $this->addFlash("login", $login ?? null);
        return $this->redirectToRoute('app_login');
    }


    /**
     * @Route("/_login", name="_login")
     */
    public function login2(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
    //     if ($this->getUser()) {
    //          return $this->redirectToRoute('app_home');
    //  }

    //     // get the login error if there is one
    //     $error = $authenticationUtils->getLastAuthenticationError();
    //     // last username entered by the user
    //     $lastUsername = $authenticationUtils->getLastUsername();

    //     return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    // 
        return $this->redirectToRoute("app_login");
}



    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}