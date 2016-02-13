<?php

namespace Ffjv\BoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LiguesType extends AbstractType
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
                    'placeholder' => 'nom de la ligue',
                    'class' => 'form-control'
                )
            ))
            ->add('tag', 'text', array(
                'label' => 'Tag* ',
                'attr' => array(
                    'placeholder' => 'tag de la ligue',
                    'class' => 'form-control')
            ))
            ->add('rna', 'text', array(
                'label' => 'Code RNA ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Code RNA de la ligue',
                    'class' => 'form-control')
            ))
            ->add('siren', 'text', array(
                'label' => 'Code Siren ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Code Siren de la ligue',
                    'class' => 'form-control')
            ))
            ->add('siret', 'text', array(
                'label' => 'Code Siret ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Code Siret de la ligue',
                    'class' => 'form-control')
            ))
            ->add('ape', 'text', array(
                'label' => 'Code Ape ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Code Ape de la ligue',
                    'class' => 'form-control')
            ))
            ->add('email', 'email', array(
                'label' => 'Email* ',
                'attr' => array(
                    'placeholder' => 'Email de la ligue',
                    'class' => 'form-control')
            ))
            ->add('telFix', 'integer', array(
                'label' => 'Tel. Fix ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Téléphone fix de la ligue',
                    'class' => 'form-control')
            ))
            ->add('telMobile', 'integer', array(
                'label' => 'Tel. Mobile ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Téléphone mobile de la ligue',
                    'class' => 'form-control')
            ))
            ->add('address1', 'text', array(
                'label' => 'Adresse ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'adresse de la ligue',
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
                'label' => 'Code Postal ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'code postal de la ligue',
                    'class' => 'form-control')
            ))
            ->add('city', 'text', array(
                'label' => 'Ville ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'ville de la ligue',
                    'class' => 'form-control')
            ))
            ->add('about', 'textarea', array(
                'label' => 'Présentation ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Présentation de la ligue',
                    'class' => 'form-control')
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ffjv\BoBundle\Entity\Ligues'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ffjv_bobundle_ligues';
    }
}
