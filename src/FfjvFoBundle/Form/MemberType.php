<?php

namespace FfjvFoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FfjvBoBundle\Entity\UserHasClubs;

class MemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roles', ChoiceType::class, [
                'choices'   => UserHasClubs::$listRoles,
                'multiple'  => true,
                'expanded'  => true,
            ])
            ->add('permissions', ChoiceType::class, [
                'choices'   => [
                    'vue' => 'VIEW',
                    'editer' => 'EDIT',
                    'Suppression' => 'DELETE'
                ],
                'multiple'  => true,
                'expanded'  => true,
            ])
            ->add('submit', SubmitType::class, [
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
