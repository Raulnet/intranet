<?php
/**
 * Created by PhpStorm.
 * User: laurentnegre
 * Date: 22/10/15
 * Time: 22:12
 */

namespace FfjvBoBundle\Service\Entity;


class UserHasTeams {

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
}