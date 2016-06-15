<?php

namespace FfjvBoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactClubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('club', HiddenType::class, array())
            ->add('user', HiddenType::class, array())
            ->add('subject', TextType::class, array(
                'label' => 'bo.form.contact.club.subject',
                'attr' => ['max' => '80']
            ))
            ->add('content', TextareaType::class, array(
                'label' => 'bo.form.contact.club.message',
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
