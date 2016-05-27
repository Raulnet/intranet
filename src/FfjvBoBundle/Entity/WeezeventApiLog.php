<?php
/**
 * Created by PhpStorm.
 * User: raulnet
 * Date: 27/05/16
 * Time: 23:19
 */

namespace FfjvBoBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * WeezeventApiLog
 *
 * @ORM\Table(name="weezevent_api_log", uniqueConstraints={@ORM\UniqueConstraint(name="api_username", columns={"api_username"})}, indexes={@ORM\Index(name="fk_weezevent_api_log_user1_idx", columns={"user_id"})})
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
     * @var \FfjvBoBundle\Entity\User
     *
     * @ORM\OneToOne(targetEntity="FfjvBoBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="usr_id", onDelete="SET NULL")
     * })
     */
    private $user;
    
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
}
