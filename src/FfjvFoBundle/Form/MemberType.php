<?php

namespace FfjvFoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FfjvBoBundle\Entity\UserHasClubs;
use AppBundle\Service\AclService;

class MemberType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $userAce = $options['user_ace'];

        $builder
            ->add('roles', ChoiceType::class, [
                'choices'   => UserHasClubs::$listRoles,
                'multiple'  => true,
                'expanded'  => true,
            ])
            ->add('permissions', ChoiceType::class, [
                'choices'   => $this->getMaskList($userAce),
                'multiple'  => true,
                'expanded'  => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'editer',
                'attr'  => ['class' => 'btn btn-success btn-sm']
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'user_ace'   => 0,
        ));
    }

    public function getName()
    {
        return 'member_type';
    }

    /**
     * @param int $userAce
     * @return array
     */
    public function getMaskList($userAce = 0){
        $list = [];
        $maskList = AclService::getMaskList();
        foreach ($maskList as $key => $mask){
            if($mask < $userAce){
                $list[$key] = $mask;
            }
        }
        return $list;
    }
}
