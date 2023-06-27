<?php

namespace App\Form;

use App\Entity\Distributeur;
use Doctrine\DBAL\Types\ArrayType;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbonnementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

       
       
        $builder->add('distributeur', EntityType::class, [
            // looks for choices from this entity
            'class' => Distributeur::class,
        
            // uses the User.username property as the visible option string
            'choice_label' => 'getNomDistributeur',
        
            // used to render a select box, check boxes or radios
            // 'multiple' => true,
            // 'expanded' => true,
        ])
        ->add("number",NumberType::class);

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
