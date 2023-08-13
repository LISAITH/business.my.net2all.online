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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;

class SecurityController extends AbstractController
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

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
     * @Route("/reset/password", name="app_reset_password")
     */
    public function resetPassword(ManagerRegistry $doctrine,Request $request,UserPasswordHasherInterface $hasher,UserRepository $repositoryUser): Response 
    {
        $entityManager = $doctrine->getManager();
        $form = $this->createFormBuilder( null, ['attr' => ['id' => 'form']] )
        ->add('email',EmailType::class,[
            'attr' => ['class' => 'form-control', 'placeholder' => "Votre Email"],
        ])
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $mail = $form->get('email')->getData();
            if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                $domain = explode('@', $mail)[1];
                if (checkdnsrr($domain, 'MX')) {
                    $user = $repositoryUser->findOneBy(["email" => $mail]);
                    if($user){
                        $password = $this->getNewPass(8);
                        $user->setPassword($hasher->hashPassword($user,$password));
                        $user->setReset($password);
                        $entityManager->persist($user);
                        $entityManager ->flush();
                        $data = [
                            'nom' => null,
                            'password' => $password,
                            'mail' => $mail
                        ];
                        $this->sendMailResetPassword($data);
                        $this->addFlash('success', 'Veuillez consulter votre boite pour vous connecter avec votre noveau mot de passe!');
                        return $this->redirectToRoute('app_login');
                    }else{
                        $this->addFlash('warning', 'L\'adresse e-mail n\'existe pas.');
                    }
                } else {
                    $this->addFlash('warning', 'L\'adresse e-mail n\'existe pas.');
                }
            } else {
                $this->addFlash('warning', 'Adresse e-mail invalide.');
            }
            return $this->redirectToRoute('app_reset_password');
        }
        
        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView()
        ]);
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

    private function sendMailResetPassword($data)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@inspections.bj', 'NET2ALL'))
            ->to($data['mail'])
            ->subject('Réinitialisation Mot de Passe')
            ->htmlTemplate('mail/reset_password.html.twig')
            ->context([
                'nom' => $data['nom'],
                'password' => $data['password'],
            ]);
        $this->mailer->send($email);
    }

    protected function getNewPass($n): string
    {
        $characters = '0123456789ABCDEFGHILKMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }
}