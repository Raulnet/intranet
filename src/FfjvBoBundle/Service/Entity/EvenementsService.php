<?php
/**
 * Created by PhpStorm.
 * User: raulnet
 * Date: 19/06/16
 * Time: 20:53
 */

namespace FfjvBoBundle\Service\Entity;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use FfjvBoBundle\Entity\Clubs;
use AppBundle\Service\WeezeventApi;

class EvenementsService
{
    const EVENT_ORIGIN_FFJV = 0;
    const EVENT_ORIGIN_WEEZ = 1;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var WeezeventApi
     */
    private $weezeventApi;

    /**
     * EvenementsService constructor.
     * @param EntityManager $em
     * @param RequestStack $request
     * @param WeezeventApi $weezeventApi
     */
    public function __construct(EntityManager $em, RequestStack  $request, WeezeventApi $weezeventApi)
    {
        $this->em           = $em;
        $this->request      = $request;
        $this->weezeventApi = $weezeventApi;
    }

    /**
     * @param Clubs $club
     * @return array
     * @throws \Exception
     */
    public function getListEventClub(Clubs $club){
        $listEventClub = $this->em->getRepository('FfjvBoBundle:Evenements')->findBy(['club' => $club]);
        $listEvents = [];
        foreach ($listEventClub as $evenements){
            $keyEvent = $evenements->getStartDate()->format('Y-m-d_H:i:s').'_ffjv';
            $listEvents[$keyEvent] = $evenements;
            $listEvents[$keyEvent]['origin'] = self::EVENT_ORIGIN_FFJV;
            $listEvents[$keyEvent]['participants'] = '0';
        }
        $apiLog = $club->getWeezeventApiLog();

        if($apiLog){
            $api = $this->weezeventApi
                ->setUser($club->getUser())
                ->setAuthAccess($apiLog->getApiUsername(), $apiLog->getApiPassword(), $apiLog->getApiKey())
                ->initConnection();
            if(!$api){
                throw new \Exception('Weez event ApiLog Failed');
            }
            $apiEvents = $api->getEvents(true);
            foreach ($apiEvents['events'] as $event){
                $keyEvent = str_replace(" ", "_", $event['date']['start']).'_weezevent';
                $listEvents[$keyEvent] = $this->convertApiEvent($event);
            }

        }
        krsort($listEvents);
        return $listEvents;
    }

    /**
     * @param $event
     * @return array
     */
    private function convertApiEvent($event){
        $eventConverted = [
            'title' => $event['name'],
            'startDate' => $event['date']['start'],
            'endDate' => $event['date']['end'],
            'origin' => self::EVENT_ORIGIN_WEEZ,
            'participants' => $event['participants']
        ];
        return $eventConverted;
    }

}