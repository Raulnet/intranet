<?php
/**
 * Created by PhpStorm.
 * User: raulnet
 * Date: 19/06/16
 * Time: 20:53
 */
namespace FfjvBoBundle\Service\Entity;

use Doctrine\ORM\EntityManager;
use FfjvBoBundle\Entity\Evenements;
use Symfony\Component\HttpFoundation\RequestStack;
use FfjvBoBundle\Entity\Clubs;
use AppBundle\Service\WeezeventApi;
use FfjvBoBundle\Service\AppService;

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
     * @var AppService
     */
    private $appService;

    /**
     * EvenementsService constructor.
     *
     * @param EntityManager $em
     * @param RequestStack  $request
     * @param WeezeventApi  $weezeventApi
     * @param AppService    $appService
     */
    public function __construct(EntityManager $em, RequestStack $request, WeezeventApi $weezeventApi, AppService $appService)
    {
        $this->em           = $em;
        $this->request      = $request;
        $this->weezeventApi = $weezeventApi;
        $this->appService   = $appService;
    }

    /**
     * @param string $zipCode
     * @param string $country
     *
     * @return string
     */
    public function getIdZipCode($zipCode = '', $country = "FR")
    {
        $zipCode = strtoupper($zipCode);
        if ($country == "FR") {
            if (strlen($zipCode) > 3) {
                return substr($zipCode, 0, 2);
            }

            return $zipCode;
        }

        return substr($zipCode, 0, 2);
    }

    /**
     * @param Clubs $club
     *
     * @return array
     * @throws \Exception
     */
    public function getListEventClub(Clubs $club)
    {
        $listEventClub = $this->em->getRepository('FfjvBoBundle:Evenements')->findBy(['club' => $club]);
        $listEvents    = [];
        foreach ($listEventClub as $evenements) {
            $keyEvent                              = $evenements->getStartDate()->format('Y-m-d_H:i:s') . '_ffjv';
            $listEvents[$keyEvent]                 = $evenements->toArray();
            $listEvents[$keyEvent]['origin']       = self::EVENT_ORIGIN_FFJV;
            $listEvents[$keyEvent]['participants'] = '0';
        }
        $apiLog = $club->getWeezeventApiLog();
        if ($apiLog) {
            $appService = $this->appService->setUser($apiLog->getUser());
            $api       = $this->weezeventApi->setAccessToken(
                $appService->deCrypt(
                    $apiLog->getApiToken()
                ))->setApiKey(
                $appService->deCrypt(
                    $apiLog->getApiKey()
                ));
            $apiEvents = $api->getEvents(true);
            foreach ($apiEvents['events'] as $event) {
                $keyEvent              = str_replace(" ", "_", $event['date']['start']) . '_weezevent';
                $listEvents[$keyEvent] = $this->convertApiEvent($event);
            }

        }
        krsort($listEvents);

        return $listEvents;
    }

    /**
     * @param $event
     *
     * @return array
     */
    private function convertApiEvent($event)
    {
        $eventConverted = [
            'title'        => $event['name'],
            'start_date'   => $event['date']['start'],
            'end_date'     => $event['date']['end'],
            'origin'       => self::EVENT_ORIGIN_WEEZ,
            'participants' => $event['participants']
        ];

        return $eventConverted;
    }

}