<?php

namespace FfjvBoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use FfjvBoBundle\Service\AppService;


class WeezeventApiLogType extends AbstractType
{
    /**
     * @var bool|AppService
     */
    private $appService = false;
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['app_service']){
            $this->appService = $options['app_service'];
        }

        $builder
            ->add('api_username', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('api_password', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('api_key', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('user_id', HiddenType::class, [])
            ->add('club_id', HiddenType::class, [])
            ->add('weezevent_api_log_id', HiddenType::class, [
                'required' => false
            ])->add('date_register', HiddenType::class, [
                'required' => false
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $log = $event->getData();
            $password = $log['api_password'];
            $apiUsername =  $log['api_username'];
            $apiKey =  $log['api_key'];
            if($this->appService){
                if($password){
                    $log['api_password'] = $this->appService->deCrypt($password);
                }
                if($apiUsername){
                    $log['api_username'] = $this->appService->deCrypt($apiUsername);
                }
                if($apiKey){
                    $log['api_key'] = $this->appService->deCrypt($apiKey);
                }
            }

            $event->setData($log);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $log = $event->getData();
            $password = $log['api_password'];
            $apiUsername =  $log['api_username'];
            $apiKey =  $log['api_key'];
            if($this->appService){
                if($password){
                    $log['api_password'] = $this->appService->crypt($password);
                }
                if($apiUsername){
                    $log['api_username'] = $this->appService->crypt($apiUsername);
                }
                if($apiKey){
                    $log['api_key'] = $this->appService->crypt($apiKey);
                }    
                $event->setData($log);
            } else {
                $event->setData([]);
            }
        
        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'app_service' => false
        ]);
    }
}
