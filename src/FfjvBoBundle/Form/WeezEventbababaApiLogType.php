<?php

namespace FfjvBoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class WeezEventbababaApiLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('apiUsername', EmailType::class, [

        ])->add('apiPassword', RepeatedType::class, array(
            'type'            => PasswordType::class,
            'invalid_message' => 'The password fields must match.',
            'first_options'   => array('label' => 'webcomponent.form.password'),
            'second_options'  => array('label' => 'webcomponent.form.repeadpassword'),
            'attr' => array('class' => 'form-control')
        ))->add('apiKey', TextType::class, []);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $log = $event->getData();
            $password = $log->getApiPassword();
            $apiUsername = $log->getApiPassword();
            $apiKey = $log->getApiKey();
            if($password){
                $log->setApiPassword(base64_decode($password));
            }
            if($apiUsername){
                $log->setApiUsername(base64_decode($apiUsername));
            }
            if($apiKey){
                $log->setApiKey(base64_decode($apiKey));
            }
            $event->setData($log);
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $log = $event->getData();
            $password1 = $log['apiPassword']['first'];
            $password2 = $log['apiPassword']['second'];
            $apiUsername =  $log['apiUsername'];
            $apiKey =  $log['apiKey'];
            if($password1){
                $log['apiPassword']['first'] = base64_encode($password1);
                $log['apiPassword']['second'] = base64_encode($password2);
            }
            if($apiUsername){
                $log['apiUsername'] = base64_encode($apiUsername);
            }
            if($apiKey){
                $log['apiKey'] = base64_encode($apiKey);
            }
            $event->setData($log);
        });
        $builder->add('submit', SubmitType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }
}
