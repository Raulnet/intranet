<?php

namespace Ffjv\BoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
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
                    'FR' => 'France',
                    'BE' => 'Belgique',
                    'CH' => 'Suisse'
                ),
                'empty_value' => 'Séléctionner votre pays',
                'attr' => array('class' => 'form-control')
            ))
            ->add('status', 'checkbox', array())
            ->add('firstName', null, array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('lastName', null, array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('birthday', 'birthday', array(
                'attr' => array('class' => 'form-control-date')
            ))
            ->add('telFix', 'text', array(
                'attr' => array('class' => 'form-control', 'max' => 20),
                'required' => false,
            ))
            ->add('telMobile', 'text', array(
                'attr' => array('class' => 'form-control', 'max' => 20),
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
            ->add('roles', 'choice', array(
                'choices' => array(
                    'ROLE_USER' => 'Utilisateur',
                    'ROLE_USER_CONFIRMED' => 'Utilisateur email confirmer',
                    'ROLE_ADMIN' => 'Administrateur',
                    'ROLE_SUPER_ADMIN' => 'Super Administrateur'
                ),
                'expanded' => true,
                'multiple' => true,
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
            ))->add('countryAddress', 'choice', array(
                'choices' => array(
                    'FR' => 'France',
                    'BE' => 'Belgique',
                    'CH' => 'Suisse'
                ),
                'empty_value' => 'Séléctionner votre pays',
                'attr' => array('class' => 'form-control')
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ffjv_bobundle_user';
    }
}
