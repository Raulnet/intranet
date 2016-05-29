<?php

namespace FfjvBoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JoinClubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('club', HiddenType::class, array())
            ->add('user', HiddenType::class, array())
            ->add('club', HiddenType::class, array())
            ->add('content', TextareaType::class, array(
                'label' =>  "bo.form.contact.club.message",
                'attr' => array(
                'placeholder' => "bo.form.contact.club.join.placeholder")
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
