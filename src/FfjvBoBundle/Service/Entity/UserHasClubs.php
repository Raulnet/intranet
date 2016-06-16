<?php
/**
 * Created by PhpStorm.
 * User: laurentnegre
 * Date: 22/10/15
 * Time: 22:14
 */

namespace FfjvBoBundle\Service\Entity;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;
use FfjvBoBundle\Form\JoinClubType;

class UserHasClubs {

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    private $formFactory;

    /**
     * UserHasClubs constructor.
     *
     * @param EntityManager      $em
     * @param RequestStack       $request
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, RequestStack  $request, ContainerInterface $container)
    {
        $this->em      = $em;
        $this->request = $request;
        $this->formFactory  = $container->get('form.factory');
    }

    /**
     * @param string $url
     * @param array  $data
     *
     * @return \Symfony\Component\Form\Form
     */
    public function getJoinClubForm($url = '', $data = array()){
        $form = $this->formFactory->create(JoinClubType::class, $data, array(
            'method' => 'POST',
            'action' => $url
        ));
        $form->add('submit', SubmitType::class, array(
            'label' => "bo.form.contact.club.send"
        ));
        return $form;
    }
}