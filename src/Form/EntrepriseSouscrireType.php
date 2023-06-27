<?php

namespace App\Form;

use App\Entity\Formule;
use App\Entity\Enseignes;
use App\Entity\PointVente;
use App\Entity\SouscriptionFormules;
use App\Repository\FormuleRepository;
use App\Repository\EnseignesRepository;
use App\Repository\PointVenteRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntrepriseSouscrireType extends AbstractType
{   
    private $user;
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->user =$options["user"];

        $builder
            ->add('enseigne', EntityType::class, [
                // looks for choices from this entity
                'class' => Enseignes::class,

                // uses the User.username property as the visible option string
                'choice_label' => 'getNomEnseigne',
                'placeholder' =>"choisir l'enseigne",
                // utiliser seulement les enseignes qui ont d'abonnement
                'query_builder' => function (EnseignesRepository $er) {
                    return $er->createQueryBuilder('e')
                    ->innerJoin("e.activationAbonnements","aa")
                    ->leftJoin("e.entreprise","ee")
                   
                    ->andWhere('ee.user = :user_id')
                    ->andWhere('e.is_validated = :status')
                    ->setParameter('status', 1)
                    ->setParameter('user_id', $this->user->getId())
                  
                    ;
                }
                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
            ->add('point_vente', EntityType::class, [
                // looks for choices from this entity
                'class' => PointVente::class,

                // uses the User.username property as the visible option string
                'choice_label' => 'getNomPointVente',
                'placeholder' =>"choisir le point de vente",
                // utiliser seulement les enseignes qui ont d'abonnement
                'query_builder' => function (PointVenteRepository $er) {
                    return $er->createQueryBuilder('p')
                    ->andWhere('p.status = :status')
                    ->setParameter('status', 1)
                    ;
                }
                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
            ->add('formule', EntityType::class, [
                // looks for choices from this entity
                'class' => Formule::class,
                "placeholder"=>"choisir la formule",

                // uses the User.username property as the visible option string
                'choice_label' => 'getNomFormule',
                // utiliser seulement  les formules actives
                'query_builder' => function (FormuleRepository $er) {
                    return  $er->createQueryBuilder('f')
                    ->andWhere('f.status = 1')
                    
                    ->orderBy('f.id', 'ASC')
                    
                   
                    ;
                },

                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SouscriptionFormules::class,
        ]);

        $resolver->setRequired(['user']);
    }
}