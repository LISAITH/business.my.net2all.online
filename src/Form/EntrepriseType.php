<?php

namespace App\Form;

use App\Entity\Entreprises;
use App\Entity\Pays;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EntrepriseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Nom"],
                "required" =>false
            ])
            ->add('prenoms',TextType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Prenoms"],
                "required" =>false
            ])
            ->add('nom_entreprise',TextType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Nom de l'entreprise"],
            ])
            ->add('num_tel',TelType::class,[
                'attr' => ['class' => 'form-control', 'placeholder' => "Tel"],
            ])
            ->add('pays',EntityType::class ,[
                "class"=>Pays::class,
                "choice_label"=>"getLibellePays",
                "placeholder"=>"Choisir le pays"
            ] )
          
            ->add('user',UserUpdateType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entreprises::class,
        ]);
    }
}
