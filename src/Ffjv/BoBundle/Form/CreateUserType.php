<?php

namespace Ffjv\BoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateUserType extends AbstractType
{
    /**
     * @var bool
     */
    private $registering = false;

    /**
     * @var bool
     */
    private $generated = false;

    /**
     * CreateUserType constructor.
     * @param bool $registering
     * @param bool $generated
     */
    public function __construct($registering = false, $generated = false)
    {
        $this->registering = $registering;
        $this->generated = $generated;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Pseudo'
                ),

            ))
            ->add('email', 'email', array(
                'attr' => array('class' => 'form-control')
            ))
            ->add('telFix', 'number', array(
                'attr' => array('class' => 'form-control'),
                'required' => false,
            ))
            ->add('telMobile', 'number', array(
                'attr' => array('class' => 'form-control'),
                'required' => false,
            ))
            ->add('nationality', 'choice', array(
                'choices' => array(
                    'FR' => 'Français',
                    'BE' => 'Belge',
                    'CH' => 'Suisse'
                ),
                'placeholder' => 'Séléctionner votre nationalité',
                'attr' => array('class' => 'form-control')
            ))
            ->add('firstName', null, array(
                'attr' => array('class' => 'form-control', 'placeholder' => 'Prenom'),

            ))
            ->add('lastName', null, array(
                'attr' => array('class' => 'form-control', 'placeholder' => 'Nom')
            ))
            ->add('birthday', 'birthday', array(
                'attr' => array('class' => 'form-control-date')
            ))
            ->add('gender', 'choice', array(
                'choices' => array(
                    'M' => 'Homme',
                    'F' => 'Femme'
                ),
                'expanded' => false,
                'multiple' => false,
                'attr' => array('class' => 'form-control')
            ))
            ->add('address1', null, array(
                'attr' => array('class' => 'form-control', 'placeholder' => 'adresse')
            ))
            ->add('address2', null, array(
                'attr' => array('class' => 'form-control'),
                'required' => false,
            ))
            ->add('zipCode', null, array(
                'attr' => array('class' => 'form-control', 'placeholder' => '75000')
            ))
            ->add('city', null, array(
                'attr' => array('class' => 'form-control', 'placeholder' => 'Paris')
            ))
            ->add('countryAddress', 'choice', array(
                'choices' => array(
                    'FR' => 'France',
                    'BE' => 'Belgique',
                    'CH' => 'Suisse'
                ),
                'placeholder' => 'Séléctionner votre pays',
                'attr' => array('class' => 'form-control')
            ));

        if(!$this->generated){
            $builder->add('password', 'repeated', array(
                'type'            => 'password',
                'invalid_message' => 'The password fields must match.',
                'options'         => array('required' => true),
                'first_options'   => array('label' => 'Password'),
                'second_options'  => array('label' => 'Repeat password'),
                'attr' => array('class' => 'form-control')
            ))
                ->add('cgu', 'checkbox', array('attr' => array()));
        }

        if(!$this->registering){
            $builder->add('status', 'checkbox', array(
                'required' => false
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getName()
    {
        return 'ffjv_bo_bundle_create_user';
    }
}
