<?php

namespace FfjvBoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JoinClubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('club', 'hidden', array())
            ->add('user', 'hidden', array())
            ->add('club', 'hidden', array())
            ->add('content', 'textarea', array(
                'label' => 'Message : ',
                'attr' => array(
                'placeholder' => 'Bonjour, je souhaiterais rejoindre votre club .')
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getName()
    {
        return 'ffjv_bo_bundle_join_club_type';
    }
}
