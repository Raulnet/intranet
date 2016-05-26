<?php

namespace FfjvFoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('email', EmailType::class, array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('nationality', ChoiceType::class, array(
                'choices_as_values' => true,
                'choices' => array(
                    'Français' => 'FR',
                    'Belge' => 'BE',
                    'Suisse' => 'CH'
                ),
                'placeholder' => 'Séléctionner votre nationalité',
                'attr' => array('class' => 'form-control')
            ))
            ->add('firstName', TextType::class, array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('lastName', TextType::class, array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('birthday', BirthdayType::class, array(
                'attr' => array('class' => 'form-control-date')
            ))
            ->add('telFix', TextType::class, array(
                'attr' => array('class' => 'form-control', 'max' => 20),
                'required' => false,
            ))
            ->add('telMobile', TextType::class, array(
                'attr' => array('class' => 'form-control', 'max' => 20),
                'required' => false,
            ))
            ->add('gender', ChoiceType::class, array(
                'choices_as_values' => true,
                'choices' => [
                    'Homme' => 'M',
                    'Femme' => 'F'
                ],
                'attr' => array('class' => 'form-control')
            ))
            ->add('address1', TextType::class, array(
                'attr' => array('class' => 'form-control', 'placeholder' => 'adresse')
            ))
            ->add('address2', TextType::class, array(
                'attr' => array('class' => 'form-control'),
                'required' => false,
            ))
            ->add('zipCode', NumberType::class, array(
                'attr' => array('class' => 'form-control', 'placeholder' => '75000')
            ))
            ->add('city', TextType::class, array(
                'attr' => array('class' => 'form-control', 'placeholder' => 'Paris')
            ))
            ->add('countryAddress', ChoiceType::class, array(
                'choices_as_values' => true,
                'choices' => array(
                    'France' => 'FR',
                    'Belgique' => 'BE',
                    'Suisse' => 'CH'
                ),
                'placeholder' => 'Séléctionner votre pays d\'adresse',
                'attr' => array('class' => 'form-control')
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }
}
