<?php

namespace App\Form;

use App\Entity\Particuliers;
use Symfony\Component\Form\AbstractType;
use App\Repository\ParticuliersRepository;
use App\Entity\ParticulierProfilProspections;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminProspectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('particulier',EntityType::class,[
                "class"=>Particuliers::class,
                "choice_label"=>"getNomPrenoms",
                'placeholder' => "choisir le particulier",
                'query_builder' => function (
                    ParticuliersRepository $er
                ) { 
                   

                    return $er->createQueryBuilder('p')
                        
                        ->leftJoin("p.particulierProfilProspections","pp")
                        ->andWhere('pp.id IS  NULL');
                },
                
               
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ParticulierProfilProspections::class,
        ]);
    }
}
