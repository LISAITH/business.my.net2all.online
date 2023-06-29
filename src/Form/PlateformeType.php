<?php

namespace App\Form;

use App\Form\UserType;
use App\Entity\Plateforme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PlateformeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Nom de la Plateforme"],
            ])
            ->add('num_tel',TextType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Téléphone de la Plateforme"],
            ])
            // ->add('cle_partenaire',TextType::class,[
            //     'attr' => ['class' => 'form-control', 'placeholder' => "Clé du Plateforme"],
            //     'constraints' => [
            //     new NotBlank([
            //         'message' => 'La clé est obligatoire ',
            //     ]),
            
            //     new Length([
            //         'min' => 3,
            //         'minMessage' => 'La clé doit contenir 3 caractère',
            //         // max length allowed by Symfony for security reasons
            //         'max' => 3,
            //     ])],
               
            //     ])
            ->add("user",UserType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plateforme::class,
        ]);
    }
}