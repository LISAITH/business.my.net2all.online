<?php

namespace App\Form;

use App\Entity\Enseignes;
use App\Entity\Abonnement;
use App\Entity\PointVente;
use App\Entity\Entreprises;
use App\Entity\ActivationAbonnements;
use Doctrine\DBAL\Query\QueryBuilder;
use App\Repository\AbonnementRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\ActivationAbonnementsRepository;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivationAbonnementType extends AbstractType
{
   private $point_vente;
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
        $this->point_vente =$options["point_vente"];
 
        $builder
            ->add('entreprise',EntityType::class,
            [
                'mapped' => false,
                'class' =>Entreprises::class,
                "choice_label"=>"getNom",
                'placeholder' => "choisir l'entreprise",
                "required"=>false
            ])
            ->add('abonnement',EntityType::class,[
                "class"=>Abonnement::class,
                "choice_label"=>"getNumeroActivation",
                'placeholder' => "choisir l'abonnement",
                'query_builder' => function (
                            AbonnementRepository $er)
                    {
                       
                        return $er->createQueryBuilder('a')
                        ->leftJoin("a.activationAbonnements","aa")
                        ->andWhere('a.status = :status')
                        ->andWhere('aa.abonnement is NULL')
                        ->andWhere('a.point_vente  =:point_vente')
                        ->setParameter('point_vente',$this->point_vente->getId())
                        ->setParameter('status', 1)
                    
                        ;
                    },
               
            ])
            ->add('enseigne',EntityType::class,[
                "class"=>Enseignes::class,
                "choice_label"=>"getNomEnseigne",
                'placeholder' => "choisir l'enseigne",
                'choice_attr' => function ($object) {
                    return ['data-pub' => $object->getEntreprise()->getId()];
                 }
              
            ])
        ;

       
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ActivationAbonnements::class,
        ]);
        $resolver->setRequired(['point_vente']);
    }
}
