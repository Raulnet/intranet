<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 05/12/15
 * Time: 20:37
 */

namespace Ffjv\BoBundle\Service\Entity;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Ffjv\BoBundle\Entity\LicencesTeams;
use Ffjv\BoBundle\Entity\Clubs;

/**
 * Class LicencesTeamsService
 * @package Ffjv\BoBundle\Service\Entity
 */
class LicencesTeamsService
{
    const PREFIX_LICENCE = 'FFJV:T';

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

        $em = $this->em;
        //create empty entity licence
        $licence = new LicencesTeams();
        $em->persist($licence);
        $em->flush();
        //set data to create licence
        $idLicence = str_pad($licence->getId(), 5, 0, STR_PAD_LEFT);
        $idClub = str_pad($club->getId(), 3, 0, STR_PAD_LEFT);
        $jd = $this->getJulianDate('now');
        // cretae number licence and flush it
        $licence->setLicence(self::PREFIX_LICENCE.$idClub.$idLicence.$jd);
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