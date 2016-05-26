<?php

namespace FfjvBoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('title', TextType::class, array(
                'label' => 'Titre* ',
                'attr' => array(
                    'placeholder' => 'nom de la ligue',
                    'class' => 'form-control'
                )
            ))
            ->add('tag', TextType::class, array(
                'label' => 'Tag* ',
                'attr' => array(
                    'placeholder' => 'tag de la ligue',
                    'class' => 'form-control')
            ))
            ->add('rna', TextType::class, array(
                'label' => 'Code RNA ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Code RNA de la ligue',
                    'class' => 'form-control')
            ))
            ->add('siren', TextType::class, array(
                'label' => 'Code Siren ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Code Siren de la ligue',
                    'class' => 'form-control')
            ))
            ->add('siret', TextType::class, array(
                'label' => 'Code Siret ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Code Siret de la ligue',
                    'class' => 'form-control')
            ))
            ->add('ape', TextType::class, array(
                'label' => 'Code Ape ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Code Ape de la ligue',
                    'class' => 'form-control')
            ))
            ->add('email', EmailType::class, array(
                'label' => 'Email* ',
                'attr' => array(
                    'placeholder' => 'Email de la ligue',
                    'class' => 'form-control')
            ))
            ->add('telFix', TextType::class, array(
                'label' => 'Tel. Fix ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Téléphone fix de la ligue',
                    'class' => 'form-control', 'max' => 20)
            ))
            ->add('telMobile', TextType::class, array(
                'label' => 'Tel. Mobile ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Téléphone mobile de la ligue',
                    'class' => 'form-control', 'max' => 20)
            ))
            ->add('address1', TextType::class, array(
                'label' => 'Adresse ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'adresse de la ligue',
                    'class' => 'form-control')
            ))
            ->add('address2', TextType::class, array(
                'label' => 'Adresse sup.',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'complément d\'adresse',
                    'class' => 'form-control')
            ))
            ->add('zipCode', TextType::class, array(
                'label' => 'Code Postal ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'code postal de la ligue',
                    'class' => 'form-control')
            ))
            ->add('city', TextType::class, array(
                'label' => 'Ville ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'ville de la ligue',
                    'class' => 'form-control')
            ))
            ->add('about', TextareaType::class, array(
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
            'data_class' => 'FfjvBoBundle\Entity\Ligues'
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
