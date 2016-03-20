<?php

namespace Ffjv\BoBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Ffjv\BoBundle\Entity\Clubs;
use Ffjv\BoBundle\Entity\User;

/**
 * UserHasClubsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserHasClubsRepository extends EntityRepository
{
    /**
     * @param Clubs $club
     * @return array
     */
    public function getRequestUserToJoin(Clubs $club)
    {
        return $this->getEntityManager()->createQuery("
            SELECT uhc, u
            FROM FfjvBoBundle:UserHasClubs uhc
            JOIN uhc.user u
            WHERE uhc.club = :club
            AND uhc.requestToJoin > 0
            ")->setParameter('club', $club)->getResult();
    }

    /**
     * @param User $user
     * @return array
     */
    public function getCLubMemberByUser(User $user){
        return $this->getEntityManager()->createQuery("
            SELECT uhc, u, c, l
            FROM FfjvBoBundle:UserHasClubs uhc
            JOIN uhc.user u
            JOIN uhc.club c
            JOIN c.licence l
            WHERE uhc.user = :user
        ")->setParameter('user', $user)->getArrayResult();
    }
}
