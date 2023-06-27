<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class RechargeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero_sous_compte',TextType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Numéro du sous compte"],
            ])
            ->add('numero_compte',TextType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Numéro de compte"],
            ])
            ->add('name_compte',TextType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Nom & Prénom / Raison sociale", 'disabled' => true],
            ])
            ->add('phone_compte',TextType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Téléphone", 'disabled' => true],
            ])
            ->add('montant',NumberType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Montant",'min'=>'100','max'=>'2000000'],
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