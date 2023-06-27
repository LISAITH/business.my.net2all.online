<?php

namespace App\Form;
use App\Entity\Distributeur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DistributeurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_distributeur',TextType::class
        )
            ->add('cle_distributeur', TextType::class ,['constraints' => [
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
            'data_class' => Distributeur::class,
        ]);
    }
}
