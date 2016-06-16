<?php

namespace FfjvBoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('username', TextType::class, array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('email', EmailType::class, array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('nationality', ChoiceType::class, array(
                'choices' => array(
                    'Séléctionner votre pays' => null,
                    'France' => 'FR',
                    'Belgique' => 'BE',
                    'Suisse' => 'CH'
                ),
                'attr' => array('class' => 'form-control')
            ))
            ->add('status', CheckboxType::class, array())
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
                'choices' => array(
                    'Homme' => 'M',
                    'Femme' => 'F'
                ),
                'expanded' => false,
                'multiple' => false,
                'attr' => array('class' => 'form-control')
            ))
            ->add('roles', ChoiceType::class, array(
                'choices' => array(
                    'Utilisateur' => 'ROLE_USER',
                    'Utilisateur email confirmer' => 'ROLE_USER_CONFIRMED',
                    'Administrateur' => 'ROLE_ADMIN',
                    'Super Administrateur' => 'ROLE_SUPER_ADMIN'
                ),
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('address1', TextType::class, array(
                'attr' => array('class' => 'form-control', 'placeholder' => 'adresse')
            ))
            ->add('address2', TextType::class, array(
                'attr' => array('class' => 'form-control'),
                'required' => false,
            ))
            ->add('zipCode', null, array(
                'attr' => array('class' => 'form-control', 'placeholder' => '75000')
            ))
            ->add('city', null, array(
                'attr' => array('class' => 'form-control', 'placeholder' => 'Paris')
            ))->add('countryAddress', ChoiceType::class, array(
                'choices' => array(
                    'Séléctionner votre pays' => null,
                    'France' => 'FR',
                    'Belgique' => 'BE',
                    'Suisse' => 'CH'
                ),
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
