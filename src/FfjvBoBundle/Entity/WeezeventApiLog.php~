<?php
/**
 * Created by PhpStorm.
 * User: raulnet
 * Date: 27/05/16
 * Time: 23:19
 */

namespace FfjvBoBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use FfjvBoBundle\Entity\Clubs;
use FfjvBoBundle\Entity\User;

/**
 * WeezeventApiLog
 *
 * @ORM\Table(name="weezevent_api_log")
 * @ORM\Entity(repositoryClass="FfjvBoBundle\Repository\WeezeventApiLog")
 */
class WeezeventApiLog
{
    /**
     * @var string
     *
     * @ORM\Column(name="api_token", type="string", length=200, nullable=false)
     */
    private $apiToken;

    /**
     * @var string
     *
     * @ORM\Column(name="api_key", type="string", length=200, nullable=false)
     */
    private $apiKey;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_register", type="datetime", nullable=false)
     */
    private $dateRegister;

    /**
     * @var integer
     *
     * @ORM\Column(name="weezevent_api_log_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="FfjvBoBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="usr_id", onDelete="SET NULL")
     * })
     */
    private $user;

    /**
     * @var Clubs
     *
     * @ORM\OneToOne(targetEntity="FfjvBoBundle\Entity\Clubs", inversedBy="weezeventApiLog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="club_id", referencedColumnName="clu_id", onDelete="SET NULL")
     * })
     */
    private $club;
        
    /**
     * WeezeventApiLog constructor.
     */
    public function __construct()
    {
        $this->dateRegister = new \DateTime('now');
    }

    /**
     * @return array
     */
    public function toArray(){
        return [
            'weezevent_api_log_id'  => $this->getId(),
            'user_id'               => ($this->getUser() ? $this->getUser()->getId() : null),
            'api_token'             => $this->apiToken,
            'api_key'               => $this->apiToken,
            'date_register'         => $this->getDateRegister()->format('Y-m-d H:i:s'),
            'club_id'               => ($this->club ? $this->club->getId() : null)
        ];
    }

    /**
     * Set apiToken
     *
     * @param string $apiToken
     *
     * @return WeezeventApiLog
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * Get apiToken
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * Set dateRegister
     *
     * @param \DateTime $dateRegister
     *
     * @return WeezeventApiLog
     */
    public function setDateRegister($dateRegister)
    {
        $this->dateRegister = $dateRegister;

        return $this;
    }

    /**
     * Get dateRegister
     *
     * @return \DateTime
     */
    public function getDateRegister()
    {
        return $this->dateRegister;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \FfjvBoBundle\Entity\User $user
     *
     * @return WeezeventApiLog
     */
    public function setUser(\FfjvBoBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \FfjvBoBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set club
     *
     * @param \FfjvBoBundle\Entity\Clubs $club
     *
     * @return WeezeventApiLog
     */
    public function setClub(\FfjvBoBundle\Entity\Clubs $club = null)
    {
        $this->club = $club;

        return $this;
    }

    /**
     * Get club
     *
     * @return \FfjvBoBundle\Entity\Clubs
     */
    public function getClub()
    {
        return $this->club;
    }

    /**
     * Set apiKey
     *
     * @param string $apiKey
     *
     * @return WeezeventApiLog
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }
}
