<?php

namespace Ffjv\FoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roles', 'choice', [
                'choices' => [
                    'ROLE_CLUB_PRESIDENT' => 'president',
                    'ROLE_CLUB_SUB_PRESIDENT' => 'vice-president',
                    'ROLE_CLUB_TREASOR' => 'tresorier',
                    'ROLE_CLUB_SUB_TREASOR' => 'vice-tresorier',
                    'ROLE_CLUB_SECRETARY' => 'secretaire',
                    'ROLE_CLUB_SUB_SECRETARY' => 'vice-secretaire',
                    'ROLE_MEMBER' => 'membre',
                ],
                'multiple' => true,
                'expanded' => true,
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
