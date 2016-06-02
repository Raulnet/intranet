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
 * @ORM\Table(name="weezevent_api_log", uniqueConstraints={@ORM\UniqueConstraint(name="api_username", columns={"api_username"})})
 * @ORM\Entity(repositoryClass="FfjvBoBundle\Repository\WeezeventApiLog")
 */
class WeezeventApiLog
{
    /**
     * @var string
     *
     * @ORM\Column(name="api_username", type="string", length=80, nullable=false)
     */
    private $apiUsername;

    /**
     * @var string
     *
     * @ORM\Column(name="api_password", type="string", length=80, nullable=false)
     */
    private $apiPassword;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="api_key", type="string", length=80, nullable=false)
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
     * Set apiUsername
     *
     * @param string $apiUsername
     *
     * @return WeezeventApiLog
     */
    public function setApiUsername($apiUsername)
    {
        $this->apiUsername = $apiUsername;

        return $this;
    }

    /**
     * Get apiUsername
     *
     * @return string
     */
    public function getApiUsername()
    {
        return $this->apiUsername;
    }

    /**
     * Set apiPassword
     *
     * @param string $apiPassword
     *
     * @return WeezeventApiLog
     */
    public function setApiPassword($apiPassword)
    {
        $this->apiPassword = $apiPassword;

        return $this;
    }

    /**
     * Get apiPassword
     *
     * @return string
     */
    public function getApiPassword()
    {
        return $this->apiPassword;
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
     * @param User $user
     *
     * @return WeezeventApiLog
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set club
     *
     * @param Clubs $club
     *
     * @return WeezeventApiLog
     */
    public function setClub(Clubs $club = null)
    {
        $this->club = $club;

        return $this;
    }

    /**
     * Get club
     *
     * @return Clubs
     */
    public function getClub()
    {
        return $this->club;
    }

    /**
     * @return array
     */
    public function toArray(){
        return [
            'weezevent_api_log_id'  => $this->getId(),
            'user_id'               => ($this->getUser() ? $this->getUser()->getId() : null),
            'api_username'          => $this->apiUsername,
            'api_password'          => $this->apiPassword,
            'api_key'               => $this->getApiKey(),
            'date_register'         => $this->getDateRegister()->format('Y-m-d H:i:s'),
            'club_id'               => ($this->club ? $this->club->getId() : null)
        ];
    }
}
