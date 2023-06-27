<?php

namespace App\Form;

use App\Entity\QuestionDecouvertes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('multiple',ChoiceType::class, array(
                'choices' => array(
                    'Question à choix multiple' => '1',
                    'Question à choix unique'=>"0"
                
                 ),
                 "placeholder" =>"Selectionner le type",
                 
                 'required' => true,
                 
               
             ))
         
        ;

        $builder->get('multiple')
            ->addModelTransformer(new CallbackTransformer(
                function ($property) {
                    return (string) $property;
                },
                function ($property) {
                    return (bool) $property;
                    }
                ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuestionDecouvertes::class,
        ]);
    }
}
