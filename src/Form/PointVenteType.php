<?php

namespace App\Form;

use App\Entity\PointVente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PointVenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_point_vente')
      
            ->add('type',ChoiceType::class,[  'choices'  => [
                "choisir"=>"",
                "Mobile"=>"1",
                "Fixe"=>"0"
            ],])
            ->add("user",UserType::class)
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PointVente::class,
        ]);
    }
}
