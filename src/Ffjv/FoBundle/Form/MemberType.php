<?php

namespace Ffjv\FoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ffjv\BoBundle\Entity\UserHasClubs;

class MemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roles', 'choice', [
                'choices'   => UserHasClubs::$listRoles,
                'multiple'  => true,
                'expanded'  => true,
            ])
            ->add('submit', 'submit', [
                'label' => 'editer',
                'attr'  => ['class' => 'btn btn-success btn-sm']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getName()
    {
        return 'ffjv_fo_bundlemember_type';
    }
}
