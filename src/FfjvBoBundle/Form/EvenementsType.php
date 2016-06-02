<?php

namespace FfjvBoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvenementsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('startDate', 'datetime')
            ->add('endDate', 'datetime')
            ->add('creationDate', 'datetime')
            ->add('lastUpdate', 'datetime')
            ->add('description')
            ->add('place')
            ->add('address1')
            ->add('address2')
            ->add('zipCode')
            ->add('idZipCode')
            ->add('city')
            ->add('country')
            ->add('visibility')
            ->add('deleted')
            ->add('close')
            ->add('useSession')
            ->add('eventWeezeventId')
            ->add('user')
            ->add('club')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FfjvBoBundle\Entity\Evenements'
        ));
    }
}
