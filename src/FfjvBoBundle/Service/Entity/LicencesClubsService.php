<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 06/12/15
 * Time: 22:10
 */

namespace FfjvBoBundle\Service\Entity;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use FfjvBoBundle\Entity\LicencesClubs;
use FfjvBoBundle\Entity\Clubs;

class LicencesClubsService
{
    const PREFIX_LICENCE = 'FFJV:C';

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
     * @param Clubs $club
     * @return LicencesTeams
     */
    public function getNewLicences(Clubs $club){

        $birthday = $club->getUser()->getBirthday();
        $prefixLicence = $this->getJulianDate($birthday->format('Y-m-d H:i:s'));

        $em = $this->em;
        //create empty entity licence
        $licence = new LicencesClubs();
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