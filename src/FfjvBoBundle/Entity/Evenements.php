<?php
/**
 * Created by PhpStorm.
 * User: raulnet
 * Date: 29/05/16
 * Time: 16:56
 */

namespace FfjvBoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FfjvBoBundle\Entity\User;
use FfjvBoBundle\Entity\Clubs;

/**
 * Class Evenements
 * @ORM\Table(name="evenements", indexes={@ORM\Index(name="fk_event_user1_idx", columns={"user_id"}), @ORM\Index(name="fk_event_club1_idx", columns={"club_id"})})
 * @ORM\Entity(repositoryClass="FfjvBoBundle\Repository\EvenementsRepository")
 * @package FfjvBoBundle\Entity
 */
class Evenements
{
    const STATUS_VISIBILITY_DRAFT = 0;
    const STATUS_VISIBILITY_PRIVATE = 1;
    const STATUS_VISIBILITY_PUBLIC = 2;

    /**
     * @var array
     */
    public static $listVisibility = [
        "entity.evenements.visibility.draft" => self::STATUS_VISIBILITY_DRAFT,
        "entity.evenements.visibility.private" => self::STATUS_VISIBILITY_PRIVATE,
        "entity.evenements.visibility.public" => self::STATUS_VISIBILITY_PUBLIC,
    ];

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=80, nullable=false)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=false)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=false)
     */
    private $endDate;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime", nullable=false)
     */
    private $creationDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_update", type="datetime", nullable=false)
     */
    private $lastUpdate;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="place", type="text", length=80, nullable=false)
     */
    private $place;

    /**
     * @var string
     *
     * @ORM\Column(name="address_1", type="text", length=100, nullable=false)
     */
    private $address1;

    /**
     * @var string
     *
     * @ORM\Column(name="address_2", type="text", length=100, nullable=true)
     */
    private $address2;

    /**
     * @var string
     *
     * @ORM\Column(name="zip_code", type="text", length=12, nullable=false)
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="id_zip_code", type="string", length=3, nullable=true)
     */
    private $idZipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="text", length=80, nullable=false)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=2, nullable=false)
     */
    private $country = 'FR';

    /**
     * @var integer
     *
     * @ORM\Column(name="visibility", type="integer", nullable=false, length=1)
     */
    private $visibility = self::STATUS_VISIBILITY_DRAFT;

    /**
     * @var integer
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=false, length=1)
     */
    private $deleted = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="close", type="boolean", nullable=false, length=1)
     */
    private $close = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="use_session", type="boolean", nullable=false, length=1)
     */
    private $useSession = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="event_weezevent_id", type="integer", nullable=true, length=11)
     */
    private $eventWeezeventId;

    /**
     * @var integer
     *
     * @ORM\Column(name="evenement_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \FfjvBoBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="FfjvBoBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="usr_id", onDelete="SET NULL")
     * })
     */
    private $user;

    /**
     * @var \FfjvBoBundle\Entity\Clubs
     *
     * @ORM\ManyToOne(targetEntity="FfjvBoBundle\Entity\Clubs", inversedBy="events")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="club_id", referencedColumnName="clu_id")
     * })
     */
    private $club;

    /**
     * Clubs constructor.
     */
    public function __construct()
    {
        $this->creationDate = new \DateTime('now');
        $this->lastUpdate   = new \DateTime('now');
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Evenements
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Evenements
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Evenements
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Evenements
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     *
     * @return Evenements
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Evenements
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set place
     *
     * @param string $place
     *
     * @return Evenements
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set address1
     *
     * @param string $address1
     *
     * @return Evenements
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * Get address1
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     *
     * @return Evenements
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * Get address2
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode
     *
     * @return Evenements
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set idZipCode
     *
     * @param string $idZipCode
     *
     * @return Evenements
     */
    public function setIdZipCode($idZipCode)
    {
        $this->idZipCode = $idZipCode;

        return $this;
    }

    /**
     * Get idZipCode
     *
     * @return string
     */
    public function getIdZipCode()
    {
        return $this->idZipCode;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Evenements
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Evenements
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set visibility
     *
     * @param integer $visibility
     *
     * @return Evenements
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Get visibility
     *
     * @return integer
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Evenements
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set close
     *
     * @param boolean $close
     *
     * @return Evenements
     */
    public function setClose($close)
    {
        $this->close = $close;

        return $this;
    }

    /**
     * Get close
     *
     * @return boolean
     */
    public function getClose()
    {
        return $this->close;
    }

    /**
     * Set useSession
     *
     * @param boolean $useSession
     *
     * @return Evenements
     */
    public function setUseSession($useSession)
    {
        $this->useSession = $useSession;

        return $this;
    }

    /**
     * Get useSession
     *
     * @return boolean
     */
    public function getUseSession()
    {
        return $this->useSession;
    }

    /**
     * Set eventWeezeventId
     *
     * @param integer $eventWeezeventId
     *
     * @return Evenements
     */
    public function setEventWeezeventId($eventWeezeventId)
    {
        $this->eventWeezeventId = $eventWeezeventId;

        return $this;
    }

    /**
     * Get eventWeezeventId
     *
     * @return integer
     */
    public function getEventWeezeventId()
    {
        return $this->eventWeezeventId;
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
     * @return Evenements
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
     * @return Evenements
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
}
