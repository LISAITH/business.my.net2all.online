<?php

namespace App\Form;

use App\Entity\Particuliers;
use App\Entity\ProfilProspections;
use Symfony\Component\Form\AbstractType;
use App\Repository\ParticuliersRepository;
use App\Entity\ParticulierProfilProspections;
use App\Repository\ProfilProspectionsRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilProspectionType extends AbstractType
{
    protected $profil_id;
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $this->profil_id = $options["profil_id"];

        $builder
            ->add('particulier')
            ->add('particulier', EntityType::class, [
                "class" => Particuliers::class,
                "choice_label" => "getNomPrenoms",
                'placeholder' => "choisir le particulier",
                'query_builder' => function (
                    ParticuliersRepository $er
                ) { 
                   

                    return $er->createQueryBuilder('p')
                        
                        ->leftJoin("p.particulierProfilProspections","pp")
                        ->andWhere('pp.id IS NULL');
                },



            ])
            ->add('profil_prospection', EntityType::class, [
                "class" => ProfilProspections::class,
                "choice_label" => "getLibelle",
                'placeholder' => "choisir le profil de prospection",
                'query_builder' => function (
                    ProfilProspectionsRepository $er
                ) { 
                   

                    return $er->createQueryBuilder('p')
                        ->andWhere('p.id < :profil_id')
                        ->setParameter('profil_id', $this->profil_id);
                },


            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ParticulierProfilProspections::class,
        ]);
        $resolver->setRequired(['profil_id']);
    }
    
}
