<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 06/12/15
 * Time: 23:37
 */

namespace FfjvBoBundle\Service\Entity;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use FfjvBoBundle\Entity\LicencesUsers;
use FfjvBoBundle\Entity\User;

class LicencesUsersService
{
    const PREFIX_LICENCE = 'FFJV:U';

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
     * @param User $user
     * @return LicencesUsers
     */
    public function getNewLicences(User $user){

        $birthday = $user->getBirthday();
        $prefixLicence = $this->getJulianDate($birthday->format('Y-m-d H:i:s'));

        $em = $this->em;
        //create empty entity licence
        $licence = new LicencesUsers();
        $em->persist($licence);
        $em->flush();
        //set data to create licence
        $idLicence = str_pad($licence->getId(), 5, 0, STR_PAD_LEFT);
        $jd = $this->getJulianDate('now');
        // create number licence and flush it
        $licence->setLicence(self::PREFIX_LICENCE.$prefixLicence.$idLicence.$jd);
        $em->persist($licence);
        $em->flush();

        return $licence;
    }

    /**
     * @param string $date
     * @return string
     */
    private function getJulianDate($date = 'now'){
        $dateTime = new \DateTime($date);
        $year = $dateTime->format('y');
        $day  = str_pad($dateTime->format('z'), 3, 0, STR_PAD_LEFT);
        return $year.$day;

    }
}