<?php

namespace App\Form;

use App\Entity\Enseignes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EnseignesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_enseigne',TextType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Nom de l'enseigne"],
            ])
            ->add('code_enseigne',TextType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Code de l'enseigne"],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enseignes::class,
        ]);
    }
}
