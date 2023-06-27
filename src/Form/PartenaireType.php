<?php

namespace App\Form;

use App\Form\UserType;
use App\Entity\Partenaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PartenaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_partenaire',TextType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Nom du Partenaire"],
            ])
            ->add('cle_partenaire',TextType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Clé du Partenaire"],
                'constraints' => [
                new NotBlank([
                    'message' => 'La clé est obligatoire ',
                ]),
            
                new Length([
                    'min' => 3,
                    'minMessage' => 'La clé doit contenir 3 caractère',
                    // max length allowed by Symfony for security reasons
                    'max' => 3,
                ])],
               
                ])
            ->add("user",UserType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Partenaire::class,
        ]);
    }
}
