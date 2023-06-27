<?php

namespace App\Form;

use App\Entity\Communaute;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommunauteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle',TextType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Nom du Communauté"],
            ])
            ->add('description',TextareaType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Description de la Communauté"],
            ])
            ->add('logo',FileType::class,[
                'attr' => ['class' => 'custom-file-input', 'id' => "exampleInputFile"],
                'data_class' => null
            ])
            ->add('url',TextType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Url de site"],
            
            ])
        
          
            ->add('app_url',TextType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Url Application"],
             
            ])

            ->add('etat',ChoiceType::class,[  'choices'  => [
                "choisir l'état du service"=>"",
                "Activé"=>"1",
                "Désaticvé"=>"0",
                
            ],'attr' => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Communaute::class,
        ]);
    }
}
