<?php

namespace Ffjv\FoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ffjv\BoBundle\Entity\UserHasTeams;

class TeamMemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user_id', 'hidden', array())
            ->add('roles', 'choice', [
                'choices'   => UserHasTeams::$listRoles,
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
        return 'ffjv_fo_bundle_team_member_type';
    }
}
