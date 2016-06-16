<?php

namespace FfjvBoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
            ->add('title', TextType::class, array(
                'label' => 'Titre* ',
                'attr' => array(
                    'placeholder' => 'nom du club',
                    'class' => 'form-control'
                )
            ))
            ->add('tag', TextType::class, array(
                'label' => 'Tag* ',
                'attr' => array(
                    'placeholder' => 'tag du club',
                    'class' => 'form-control')
            ))
            ->add('rna', TextType::class, array(
                'label' => 'Code RNA* ',
                'attr' => array(
                    'placeholder' => 'Code RNA de l\'association',
                    'class' => 'form-control')
            ))
            ->add('siren', TextType::class, array(
                'label' => 'Code Siren ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Code Siren de l\'association',
                    'class' => 'form-control')
            ))
            ->add('siret', TextType::class, array(
                'label' => 'Code Siret ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Code Siret de l\'association',
                    'class' => 'form-control')
            ))
            ->add('ape', TextType::class, array(
                'label' => 'Code Ape ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Code Ape de l\'association',
                    'class' => 'form-control')
            ))
            ->add('email', EmailType::class, array(
                'label' => 'Email* ',
                'attr' => array(
                    'placeholder' => 'Email de l\'association',
                    'class' => 'form-control')
            ))
            ->add('telFix', TextType::class, array(
                'label' => 'Tel. Fix ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Téléphone fix de l\'association',
                    'class' => 'form-control',
                    'max' => 20)
            ))
            ->add('telMobile', TextType::class, array(
                'label' => 'Tel. Mobile ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Téléphone mobile de l\'association',
                    'class' => 'form-control',
                    'max' => 20)
            ))
            ->add('country', ChoiceType::class, array(
                'placeholder' => 'Séléctionner le pays de l\'association',
                'choices' => array(
                    'France' => 'FR',
                    'Belgique' => 'BE',
                    'Suisse' => 'CH'
                ),
                'attr' => array(
                    'class' => 'form-control')
            ))
            ->add('address1', TextType::class, array(
                'label' => 'Adresse* ',
                'attr' => array(
                    'placeholder' => 'adresse de l\'association',
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
                'label' => 'Code Postal* ',
                'attr' => array(
                    'placeholder' => 'code postal de l\'association',
                    'class' => 'form-control')
            ))
            ->add('city', TextType::class, array(
                'label' => 'Ville* ',
                'attr' => array(
                    'placeholder' => 'ville de l\'association',
                    'class' => 'form-control')
            ))
            ->add('about', TextareaType::class, array(
                'label' => 'Présentation ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Présentation de l\'association',
                    'class' => 'form-control')
            ))
            ->add('ligues', null, array(
                'label' => 'Ligues ',
                'required' => false,
                'placeholder' => 'Sélectionner une ligues',
                'attr' => array(
                    'class' => 'form-control')))
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event){
               $club = $event->getData();
                if($club['about']){
                    $about = $club['about'];
                    $about = str_replace('script', '', $about);
                    $club['about'] = $about;
                    $event->setData($club);
                }
            });
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
