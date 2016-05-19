<?php

namespace FfjvBoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactClubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('club', 'hidden', array())
            ->add('user', 'hidden', array())
            ->add('subject', 'text', array(
                'max_length' => '80',
                'label' => 'Sujet :'
            ))
            ->add('content', 'textarea', array(
                'label' => 'Message : ',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getName()
    {
        return 'ffjv_bo_bundle_contact_club_type';
    }
}
