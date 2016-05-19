<?php
/**
 * Created by PhpStorm.
 * User: laurentnegre
 * Date: 16/01/2016
 * Time: 13:36
 */
namespace FfjvBoBundle\Service\Entity;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;

class MessagesServices
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @param EntityManager $em
     * @param RequestStack  $request
     */
    public function __construct(EntityManager $em, RequestStack  $request)
    {
        $this->em      = $em;
        $this->request = $request;
    }

    /**
     * @return array|\FfjvBoBundle\Entity\User[]
     */
    public function findAll(){
        return $this->em->getRepository('FfjvBoBundle:Messages')->findAll();
    }

    /**
     * @param int $number
     *
     * @return array
     */
    public function getLastSend($number = 10 ){
        return $this->em->getRepository('FfjvBoBundle:Messages')->getLastSend($number);
    }
}