<?php
namespace Ffjv\FoBundle\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SendMailType extends AbstractType
{
    /**
     * @var int
     */
    private $userId = 0;

    public function __construct($userId = '')
    {
        $this->userId = $userId;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userId', HiddenType::class, array(
                'attr' => array(
                    'value' => $this->userId
                )
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'cliquez-ici'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getName()
    {
        return 'ffjv_fo_bundle_send_mail';
    }
}
