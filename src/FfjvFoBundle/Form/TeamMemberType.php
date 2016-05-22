<?php

namespace FfjvFoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FfjvBoBundle\Entity\UserHasTeams;

class TeamMemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user_id', HiddenType::class, array())
            ->add('roles', ChoiceType::class, [
                'choices'   => UserHasTeams::$listRoles,
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
        return 'ffjv_fo_bundle_team_member_type';
    }
}
