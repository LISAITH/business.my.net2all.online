<?php

namespace App\Form;

use App\Entity\Enseignes;
use App\Entity\Formule;
use App\Entity\SouscriptionFormules;
use App\Repository\EnseignesRepository;
use App\Repository\FormuleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SouscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
                    ->andWhere('e.status = :status')
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
    }
}