<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\SousCompte;
use App\Entity\CompteEcash;
use App\Entity\Entreprises;
use App\Entity\Particuliers;
use App\Form\RegistrationFormType;
use App\Repository\PaysRepository;
use App\Repository\TypeRepository;
use App\Repository\UserRepository;
use App\Repository\ServicesRepository;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator, EntityManagerInterface $entityManager, TypeRepository $repositoryType,PaysRepository $repositoryPays,UserRepository $repositoryUser, ServicesRepository $servicesRepository): Response
    {
        
        $validator = Validation::createValidator();

        $user = new User();
        $particulier=new Particuliers();
        $entreprise=new Entreprises();
        $pays_all=$repositoryPays->findAll();
        $user->setStatus(true);

        /// Generate new accountNumber for user's CompteEcash
        $accountNumber = $this->getRandomText(10);

        /// Create user's CompteEcash
        $ecash = new CompteEcash();
        $ecash->setNumeroCompte($accountNumber);
        $ecash->setSolde(0);

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $constraint = [
                "type" => [
                    new Assert\NotBlank(),
                    new Assert\Choice([1, 6])
                ],
                "nom" => [
                    new Assert\NotBlank(),
                    new Assert\Length(["min" => 1])
                ],
                "prenoms" => [
                    new Assert\NotBlank(),
                    new Assert\Length(["min" => 1])
                ],
            ];
    
            if ($request->request->get('type') == 1) {
                $constraint["genre"] = [
                    new Assert\NotBlank(),
                    new Assert\Choice(["m", "f"])
                ];
            } else if ($request->request->get('type')== 6) {
                $constraint["nom_entreprise"] = [
                    new Assert\NotBlank(),
                    new Assert\Length(["min" => 1])
                ];
            }

            // Create [Assert\Collection] to validate request data
            $constraints = new Assert\Collection($constraint);

            $errors = $validator->validate($request->getContent(), $constraints);
            
            $pays=$repositoryPays->find($request->request->get('pays'));
            $user->setPassword($userPasswordHasher->hashPassword($user,$form->get('plainPassword')->getData()));

            if($request->request->get('type')==6){
                $type = $repositoryType->find($request->request->get('type'));
                $user->setType($type);
                $entreprise->setNom($request->request->get('nom'));
                $entreprise->setPrenoms($request->request->get('prenoms'));
                $entreprise->setNomEntreprise($request->request->get('nom_entreprise'));
                $entreprise->setNumTel($form->get('numero')->getData());
                $entreprise->setPays($pays);
                $entreprise->setUser($user);

                // Set [CompteEcash]
                $ecash->setEntreprise($entreprise);

                /// Save enterprise to database
                $entityManager->persist($entreprise);

            }else if($request->request->get('type')==1){
                $type = $repositoryType->find($request->request->get('type'));
                
                $user->setType($type);
                $particulier->setNom($request->request->get('nom'));
                $particulier->setPrenoms($request->request->get('prenoms'));
                $particulier->setNumTel($form->get('numero')->getData());
                $particulier->setGenre($request->request->get('genre'));
                $particulier->setPays($pays);
                $particulier->setUser($user);

                // Set [CompteEcash]
                $ecash->setParticulier($particulier);

                /// Save particular to database
                $entityManager->persist($particulier);

            }

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

            $entityManager->flush();
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
            'pays_all'=>$pays_all
        ]);
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
}