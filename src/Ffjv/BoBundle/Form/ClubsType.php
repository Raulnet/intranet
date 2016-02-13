<?php

namespace Ffjv\BoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClubsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'Titre* ',
                'attr' => array(
                    'placeholder' => 'nom du club',
                    'class' => 'form-control'
                )
            ))
            ->add('tag', 'text', array(
                'label' => 'Tag* ',
                'attr' => array(
                    'placeholder' => 'tag du club',
                    'class' => 'form-control')
            ))
            ->add('rna', 'text', array(
                'label' => 'Code RNA* ',
                'attr' => array(
                    'placeholder' => 'Code RNA de l\'association',
                    'class' => 'form-control')
            ))
            ->add('siren', 'text', array(
                'label' => 'Code Siren ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Code Siren de l\'association',
                    'class' => 'form-control')
            ))
            ->add('siret', 'text', array(
                'label' => 'Code Siret ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Code Siret de l\'association',
                    'class' => 'form-control')
            ))
            ->add('ape', 'text', array(
                'label' => 'Code Ape ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Code Ape de l\'association',
                    'class' => 'form-control')
            ))
            ->add('email', 'email', array(
                'label' => 'Email* ',
                'attr' => array(
                    'placeholder' => 'Email de l\'association',
                    'class' => 'form-control')
            ))
            ->add('telFix', 'integer', array(
                'label' => 'Tel. Fix ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Téléphone fix de l\'association',
                    'class' => 'form-control')
            ))
            ->add('telMobile', 'integer', array(
                'label' => 'Tel. Mobile ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Téléphone mobile de l\'association',
                    'class' => 'form-control')
            ))
            ->add('country', 'choice', array(
                'empty_value' => 'Séléctionner le pays de l\'association',
                'choices' => array(
                    'FR' => 'France',
                    'BE' => 'Belgique',
                    'CH' => 'Suisse'
                ),
                'attr' => array(
                    'class' => 'form-control')
            ))
            ->add('address1', 'text', array(
                'label' => 'Adresse* ',
                'attr' => array(
                    'placeholder' => 'adresse de l\'association',
                    'class' => 'form-control')
            ))
            ->add('address2', 'text', array(
                'label' => 'Adresse sup.',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'complément d\'adresse',
                    'class' => 'form-control')
            ))
            ->add('zipCode', 'text', array(
                'label' => 'Code Postal* ',
                'attr' => array(
                    'placeholder' => 'code postal de l\'association',
                    'class' => 'form-control')
            ))
            ->add('city', 'text', array(
                'label' => 'Ville* ',
                'attr' => array(
                    'placeholder' => 'ville de l\'association',
                    'class' => 'form-control')
            ))
            ->add('about', 'textarea', array(
                'label' => 'Présentation ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Présentation de l\'association',
                    'class' => 'form-control')
            ))
            ->add('ligues', null, array(
                'label' => 'Ligues ',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control')
            ))
        ;
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
        return 'ffjv_bobundle_clubs';
    }
}
