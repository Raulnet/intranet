<?php
/**
 * Created by PhpStorm.
 * User: laurentnegre
 * Date: 22/10/15
 * Time: 22:10
 */

namespace FfjvBoBundle\Service\Entity;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use FfjvBoBundle\Entity\User;

class UserService {

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
     * @return array|\FfjvBoBundle\Entity\User[]
     */
    public function findAll(){
        return $this->em->getRepository('FfjvBoBundle:User')->findAll();
    }

    /**
     * @param int $number
     *
     * @return array
     */
    public function getLastRegister($number = 10 ){
        return $this->em->getRepository('FfjvBoBundle:User')->getLastRegister($number);
    }

    /**
     * @return array
     */
    public function getCountByCountry(){
        return $this->em->getRepository('FfjvBoBundle:User')->countByCountry();
    }

    /**
     * @return array
     */
    public function getCountByIdZipCode(){
        return $this->em->getRepository('FfjvBoBundle:User')->countByIdZipCode();
    }

    /**
     * @param $userId
     *
     * @return \FfjvBoBundle\Entity\User|null|object
     */
    public function findOneById($userId){
        return $this->em->getRepository('FfjvBoBundle:User')->findOneBy(array('id' => $userId));
    }

    /**
     * @param $userUsername
     *
     * @return \FfjvBoBundle\Entity\User|null|object
     */
    public function findOneByUsername($userUsername){
        return $this->em->getRepository('FfjvBoBundle:User')->findOneBy(array('username' => $userUsername));
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

    /**
     * @param User $user
     * @return bool
     */
    public function saveUserEntity(User $user){
        
        $this->em->persist($user);
        $this->em->flush();
        return true;        
        
    }

}