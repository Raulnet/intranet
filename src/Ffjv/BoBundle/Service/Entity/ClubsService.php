<?php
/**
 * Created by PhpStorm.
 * User: laurentnegre
 * Date: 22/10/15
 * Time: 21:47
 */
namespace Ffjv\BoBundle\Service\Entity;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;

class ClubsService {

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
     * @return array|\Ffjv\BoBundle\Entity\Clubs[]
     */
    public function findAll(){
        return $this->em->getRepository('FfjvBoBundle:Clubs')->findAll();
    }

    /**
     * @param $id
     *
     * @return \Ffjv\BoBundle\Entity\Clubs
     */
    public function findOneById($id){
        return $this->em->getRepository('FfjvBoBundle:Clubs')->findOneById($id);
    }

    /**
     * @param int $number
     *
     * @return array
     */
    public function getLastCreated($number = 10){
        return $this->em->getRepository('FfjvBoBundle:Clubs')->getLastCreated($number);
    }

    /**
     * @return array
     */
    public function getCountByIdZipCode(){
        return $this->em->getRepository('FfjvBoBundle:Clubs')->countByIdZipCode();
    }

    /**
     * @param $clubId
     *
     * @return array
     */
    public function getCountMemberActive($clubId = 0){
        return $this->em->getRepository('FfjvBoBundle:Clubs')->countMemberActive($clubId);
    }

    /**
     * @param string $zipCode
     * @param string $country
     *
     * @return string
     */
    public function getIdZipCode($zipCode, $country = "FR"){

        $zipCode = strtoupper($zipCode);
        if($country == "FR"){
            if(strlen($zipCode) > 3){
                return substr($zipCode, 0, 2);
            }
            return $zipCode;
        }
        return substr($zipCode, 0, 2);
    }




}