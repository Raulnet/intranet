<?php

namespace Ffjv\FoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('email', 'email', array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('nationality', 'choice', array(
                'choices' => array(
                    'FR' => 'Français',
                    'BE' => 'Belge',
                    'CH' => 'Suisse'
                ),
                'empty_value' => 'Séléctionner votre nationalité',
                'attr' => array('class' => 'form-control')
            ))
            ->add('firstName', null, array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('lastName', null, array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('birthday', 'birthday', array(
                'attr' => array('class' => 'form-control-date')
            ))
            ->add('telFix', 'number', array(
                'attr' => array('class' => 'form-control'),
                'required' => false,
            ))
            ->add('telMobile', 'number', array(
                'attr' => array('class' => 'form-control'),
                'required' => false,
            ))
            ->add('gender', 'choice', array(
                'choices' => array(
                    'M' => 'Homme',
                    'F' => 'Femme'
                ),
                'expanded' => false,
                'multiple' => false,
                'attr' => array('class' => 'form-control')
            ))
            ->add('address1', null, array(
                'attr' => array('class' => 'form-control', 'placeholder' => 'adresse')
            ))
            ->add('address2', null, array(
                'attr' => array('class' => 'form-control'),
                'required' => false,
            ))
            ->add('zipCode', null, array(
                'attr' => array('class' => 'form-control', 'placeholder' => '75000')
            ))
            ->add('city', null, array(
                'attr' => array('class' => 'form-control', 'placeholder' => 'Paris')
            ))
            ->add('countryAddress', 'choice', array(
                'choices' => array(
                    'FR' => 'France',
                    'BE' => 'Belgique',
                    'CH' => 'Suisse'
                ),
                'empty_value' => 'Séléctionner votre pays d\'adresse',
                'attr' => array('class' => 'form-control')
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getName()
    {
        return 'ffjv_fo_bundle_update_profile_type';
    }
}
