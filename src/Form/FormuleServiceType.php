<?php

namespace App\Form;

use App\Entity\Formule;
use App\Entity\Services;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormuleServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('formule', EntityType::class, [
                // looks for choices from this entity
                'class' => Formule::class,
            
                // uses the User.username property as the visible option string
                'choice_label' => 'getNomFormule',
            
                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
            ->add('service', EntityType::class, [
                // looks for choices from this entity
                'class' => Services::class,
            
                // uses the User.username property as the visible option string
                'choice_label' => 'getLibelle',
            
                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
