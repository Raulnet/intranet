<?php

namespace FfjvBoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class WeezeventApiLogType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('api_username', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('api_password', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('api_key', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('user_id', HiddenType::class, [])
            ->add('club_id', HiddenType::class, []);

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {

    }
}
