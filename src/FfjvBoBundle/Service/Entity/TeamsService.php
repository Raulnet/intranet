<?php
/**
 * Created by PhpStorm.
 * User: laurentnegre
 * Date: 22/10/15
 * Time: 22:03
 */

namespace FfjvBoBundle\Service\Entity;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;

class TeamsService {

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
     * @return array|\FfjvBoBundle\Entity\Teams[]
     */
    public function findAll(){
        return $this->em->getRepository('FfjvBoBundle:Teams')->findAll();
    }

    /**
     * @param int $number
     *
     * @return array
     */
    public function getLastRegistered($number = 5){
        return $this->em->getRepository('FfjvBoBundle:Teams')->getLastRegistered($number);
    }


}