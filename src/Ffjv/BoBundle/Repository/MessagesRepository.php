<?php

namespace Ffjv\BoBundle\Repository;
use Ffjv\BoBundle\Entity\Clubs;
use Ffjv\BoBundle\Entity\Messages;
use Ffjv\BoBundle\Entity\User;

/**
 * MessagesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessagesRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param int $number
     *
     * @return array
     */
    public function getLastSend($number = 10){

        return $this->getEntityManager()->createQuery(
            "SELECT m.type,
                    m.id,
                    m.message,
                    m.subject,
                    m.creationDate,
                    aut.username as author, aut.id as authorId,
                    c.title as clubTitle, c.id as clubId,
                    l.title as ligueTitle, l.id as ligueId,
                    u.username as username, u.id as userId
            FROM FfjvBoBundle:Messages m
            LEFT JOIN m.authorUser aut
            LEFT JOIN m.user u
            LEFT JOIN m.club c
            LEFT JOIN m.ligue l
            ORDER BY m.creationDate DESC
            "
        )->setMaxResults($number)->getArrayResult();
    }

    /**
     * @param Clubs $club
     * @param User $user
     * @return array
     */
    public function getLastRequestJoinClubByUser(Clubs $club, User $user){
        $message = new Messages();
        return $this->getEntityManager()->createQuery(
            "SELECT m.type,
                    m.id,
                    m.message,
                    m.subject,
                    m.creationDate,
                    aut.username as author, aut.id as authorId,
                    c.title as clubTitle, c.id as clubId
            FROM FfjvBoBundle:Messages m
            LEFT JOIN m.authorUser aut
            LEFT JOIN m.club c
            WHERE c = :club
            AND aut = :user
            AND m.type = :type
            "
        )->setParameters(['club'=> $club, 'user' => $user, 'type' => $message::REQUEST_JOIN_CLUB])->getResult();
    }
}
