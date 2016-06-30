<?php

namespace FfjvBoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use FfjvBoBundle\Entity\Evenements;

class EvenementsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('club', null, [
                'placeholder' => 'Séléctionner un club',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('title', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('startDate', DateTimeType::class, [
                'attr' => ['class' => 'form-control-date']
                
            ])
            ->add('endDate', DateTimeType::class, [
                'attr' => ['class' => 'form-control-date']
            ])
            ->add('description', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('place', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('address1', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('address2', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('zipCode', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('city', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('country', ChoiceType::class, [
                'choices' => [
                    'Séléctionner votre pays' => null,
                    'France' => 'FR',
                    'Belgique' => 'BE',
                    'Suisse' => 'CH'
                ],
                'attr' => ['class' => 'form-control']
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FfjvBoBundle\Entity\Evenements'
        ));
    }
}
