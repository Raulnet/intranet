<?php

namespace FfjvBoBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TeamsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TeamsRepository extends EntityRepository
{

    /**
     * @param int $number
     *
     * @return array
     */
    public function getLastRegistered($number = 5)
    {
        return $this->getEntityManager()->createQuery("SELECT t.id, t.title, t.creationDate, l.licence as licence, c.title as club, c.id as clubId
            FROM FfjvBoBundle:Teams t
            LEFT JOIN t.licence l
            LEFT JOIN t.club c
            ORDER BY t.creationDate DESC
            ")->setMaxResults($number)->getArrayResult();
    }


}
