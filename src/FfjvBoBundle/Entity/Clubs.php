<?php

namespace FfjvBoBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use FfjvBoBundle\Entity\WeezeventApiLog;

/**
 * Clubs
 *
 * @ORM\Table(name="t_clubs", uniqueConstraints={@ORM\UniqueConstraint(name="title_UNIQUE", columns={"clu_title"}), @ORM\UniqueConstraint(name="UNIQ_1B0C575A26EF07C9", columns={"licence_id"})}, indexes={@ORM\Index(name="fk_clubs_user1_idx", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="FfjvBoBundle\Repository\ClubsRepository")
 * @UniqueEntity("title", message="Ce titre de club existe déjà ?!")
 * @UniqueEntity("tag", message="Ce tag de club existe déjà ?!")
 * @UniqueEntity("rna", message="Ce code RNA existe déjà ?!")
 * @UniqueEntity("email", message="Cet email est déjà utilisé !")
 */
class Clubs
{
    /**
     * @var string $title
     *
     * @ORM\Column(name="clu_title", type="string", length=80, nullable=false, unique=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="clu_tag", type="string", length=10, nullable=false, unique=true)
     */
    private $tag;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="clu_creation_date", type="datetime", nullable=false)
     */
    private $creationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="clu_rna", type="string", length=10, nullable=false, unique=true)
     */
    private $rna;

    /**
     * @var integer
     *
     * @ORM\Column(name="clu_siren", type="integer", nullable=true)
     */
    private $siren;

    /**
     * @var integer
     *
     * @ORM\Column(name="clu_siret", type="integer", nullable=true)
     */
    private $siret;

    /**
     * @var string
     *
     * @ORM\Column(name="clu_ape", type="string", length=5, nullable=true)
     */
    private $ape;

    /**
     * @var string
     *
     * @ORM\Column(name="clu_email", type="string", length=100, nullable=false, unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var integer
     *
     * @ORM\Column(name="clu_tel_fix", type="string", length=20, nullable=true)
     */
    private $telFix;

    /**
     * @var integer
     *
     * @ORM\Column(name="clu_tel_mobile", type="string", length=20, nullable=true)
     */
    private $telMobile;

    /**
     * @var string
     *
     * @ORM\Column(name="clu_country", type="string", length=2, nullable=false)
     */
    private $country = 'FR';

    /**
     * @var string
     *
     * @ORM\Column(name="clu_address_1", type="string", length=100, nullable=false)
     */
    private $address1;

    /**
     * @var string
     *
     * @ORM\Column(name="clu_address_2", type="string", length=100, nullable=true)
     */
    private $address2;

    /**
     * @var string
     *
     * @ORM\Column(name="clu_zip_code", type="string", length=12, nullable=false)
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="clu_id_zip_code", type="string", length=3, nullable=true)
     */
    private $idZipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="clu_city", type="string", length=64, nullable=false)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="clu_about", type="text", nullable=true)
     */
    private $about;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="clu_last_update", type="datetime", nullable=false)
     */
    private $lastUpdate;

    /**
     * @var string
     *
     * @ORM\Column(name="clu_activation_code", type="string", length=100, nullable=false)
     */
    private $activationCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="clu_validate", type="integer", nullable=false)
     */
    private $validate = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="clu_status", type="boolean", nullable=false)
     */
    private $status = false;

    /**
     * @var null
     * @ORM\OneToOne(targetEntity="FfjvBoBundle\Entity\WeezeventApiLog", mappedBy="club", cascade={"persist", "remove"})
     */
    private $weezeventApiLog = null;

    /**
     * @var integer
     *
     * @ORM\Column(name="clu_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \FfjvBoBundle\Entity\LicencesClubs
     *
     * @ORM\ManyToOne(targetEntity="FfjvBoBundle\Entity\LicencesClubs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="licence_id", referencedColumnName="lic_id")
     * })
     */
    private $licence;

    /**
     * @var \FfjvBoBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="FfjvBoBundle\Entity\User", inversedBy="clubs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="usr_id", onDelete="SET NULL")
     * })
     */
    private $user;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="FfjvBoBundle\Entity\Ligues", inversedBy="clubs")
     * @ORM\JoinTable(name="t_clubs_has_ligues",
     *   joinColumns={
     *     @ORM\JoinColumn(name="t_club_id", referencedColumnName="clu_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="t_ligue_id", referencedColumnName="lig_id")
     *   }
     * )
     */
    private $ligues;

    /**
     * @var ArrayCollection $teams
     *
     * @ORM\OneToMany(targetEntity="FfjvBoBundle\Entity\Teams", mappedBy="club", cascade={"persist", "remove"})
     */
    private $teams;

    /**
     * @var ArrayCollection $events
     *
     * @ORM\OneToMany(targetEntity="FfjvBoBundle\Entity\Evenements", mappedBy="club", cascade={"persist", "remove"})
     */
    private $events;

    /**
     * @var ArrayCollection $messages
     *
     * @ORM\OneToMany(targetEntity="FfjvBoBundle\Entity\Messages", mappedBy="club")
     */
    private $messages;

    /**
     * @var ArrayCollection $members
     *
     * @ORM\OneToMany(targetEntity="FfjvBoBundle\Entity\UserHasClubs", mappedBy="club", cascade={"persist", "remove"})
     */
    private $members = array();

    /**
     * Clubs constructor.
     */
    public function __construct()
    {
        $this->creationDate = new \DateTime('now');
        $this->lastUpdate   = new \DateTime('now');
        $this->ligues = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->members = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->activationCode = base_convert(md5(uniqid(mt_rand(), true)), 16, 36);
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->title;
    }
    
    /**
     * Set title
     *
     * @param string $title
     *
     * @return Clubs
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
     * Set tag
     *
     * @param string $tag
     *
     * @return Clubs
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Clubs
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
     * Set rna
     *
     * @param string $rna
     *
     * @return Clubs
     */
    public function setRna($rna)
    {
        $this->rna = $rna;

        return $this;
    }

    /**
     * Get rna
     *
     * @return string
     */
    public function getRna()
    {
        return $this->rna;
    }

    /**
     * Set siren
     *
     * @param integer $siren
     *
     * @return Clubs
     */
    public function setSiren($siren)
    {
        $this->siren = $siren;

        return $this;
    }

    /**
     * Get siren
     *
     * @return integer
     */
    public function getSiren()
    {
        return $this->siren;
    }

    /**
     * Set siret
     *
     * @param integer $siret
     *
     * @return Clubs
     */
    public function setSiret($siret)
    {
        $this->siret = $siret;

        return $this;
    }

    /**
     * Get siret
     *
     * @return integer
     */
    public function getSiret()
    {
        return $this->siret;
    }

    /**
     * Set ape
     *
     * @param string $ape
     *
     * @return Clubs
     */
    public function setApe($ape)
    {
        $this->ape = $ape;

        return $this;
    }

    /**
     * Get ape
     *
     * @return string
     */
    public function getApe()
    {
        return $this->ape;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Clubs
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telFix
     *
     * @param integer $telFix
     *
     * @return Clubs
     */
    public function setTelFix($telFix)
    {
        $this->telFix = $telFix;

        return $this;
    }

    /**
     * Get telFix
     *
     * @return integer
     */
    public function getTelFix()
    {
        return $this->telFix;
    }

    /**
     * Set telMobile
     *
     * @param integer $telMobile
     *
     * @return Clubs
     */
    public function setTelMobile($telMobile)
    {
        $this->telMobile = $telMobile;

        return $this;
    }

    /**
     * Get telMobile
     *
     * @return integer
     */
    public function getTelMobile()
    {
        return $this->telMobile;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Clubs
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
     * Set address1
     *
     * @param string $address1
     *
     * @return Clubs
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
     * @return Clubs
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
     * @return Clubs
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
     * @return Clubs
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
     * @return Clubs
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
     * Set about
     *
     * @param string $about
     *
     * @return Clubs
     */
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Get about
     *
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     *
     * @return Clubs
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
     * Set activationCode
     *
     * @param string $activationCode
     *
     * @return Clubs
     */
    public function setActivationCode($activationCode)
    {
        $this->activationCode = $activationCode;

        return $this;
    }

    /**
     * Get activationCode
     *
     * @return string
     */
    public function getActivationCode()
    {
        return $this->activationCode;
    }

    /**
     * Set validate
     *
     * @param integer $validate
     *
     * @return Clubs
     */
    public function setValidate($validate)
    {
        $this->validate = $validate;

        return $this;
    }

    /**
     * Get validate
     *
     * @return integer
     */
    public function getValidate()
    {
        return $this->validate;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Clubs
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
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
     * Set licence
     *
     * @param \FfjvBoBundle\Entity\LicencesClubs $licence
     *
     * @return Clubs
     */
    public function setLicence(\FfjvBoBundle\Entity\LicencesClubs $licence = null)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get licence
     *
     * @return \FfjvBoBundle\Entity\LicencesClubs
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set user
     *
     * @param \FfjvBoBundle\Entity\User $user
     *
     * @return Clubs
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
     * Add ligue
     *
     * @param \FfjvBoBundle\Entity\Ligues $ligue
     *
     * @return Clubs
     */
    public function addLigue(\FfjvBoBundle\Entity\Ligues $ligue)
    {
        $this->ligues[] = $ligue;

        return $this;
    }

    /**
     * Remove ligue
     *
     * @param \FfjvBoBundle\Entity\Ligues $ligue
     */
    public function removeLigue(\FfjvBoBundle\Entity\Ligues $ligue)
    {
        $this->ligues->removeElement($ligue);
    }

    /**
     * Get ligues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLigues()
    {
        return $this->ligues;
    }

    /**
     * Add team
     *
     * @param \FfjvBoBundle\Entity\Teams $team
     *
     * @return Clubs
     */
    public function addTeam(\FfjvBoBundle\Entity\Teams $team)
    {
        $this->teams[] = $team;

        return $this;
    }

    /**
     * Remove team
     *
     * @param \FfjvBoBundle\Entity\Teams $team
     */
    public function removeTeam(\FfjvBoBundle\Entity\Teams $team)
    {
        $this->teams->removeElement($team);
    }

    /**
     * Get teams
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * Add message
     *
     * @param \FfjvBoBundle\Entity\Messages $message
     *
     * @return Clubs
     */
    public function addMessage(\FfjvBoBundle\Entity\Messages $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Remove message
     *
     * @param \FfjvBoBundle\Entity\Messages $message
     */
    public function removeMessage(\FfjvBoBundle\Entity\Messages $message)
    {
        $this->messages->removeElement($message);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Add member
     *
     * @param \FfjvBoBundle\Entity\UserHasClubs $member
     *
     * @return Clubs
     */
    public function addMember(\FfjvBoBundle\Entity\UserHasClubs $member)
    {
        $this->members[] = $member;

        return $this;
    }

    /**
     * Remove member
     *
     * @param \FfjvBoBundle\Entity\UserHasClubs $member
     */
    public function removeMember(\FfjvBoBundle\Entity\UserHasClubs $member)
    {
        $this->members->removeElement($member);
    }

    /**
     * Get members
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Add event
     *
     * @param \FfjvBoBundle\Entity\Evenements $event
     *
     * @return Clubs
     */
    public function addEvent(\FfjvBoBundle\Entity\Evenements $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * Remove event
     *
     * @param \FfjvBoBundle\Entity\Evenements $event
     */
    public function removeEvent(\FfjvBoBundle\Entity\Evenements $event)
    {
        $this->events->removeElement($event);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Set weezeventApiLog
     *
     * @param \FfjvBoBundle\Entity\WeezeventApiLog $weezeventApiLog
     *
     * @return Clubs
     */
    public function setWeezeventApiLog(\FfjvBoBundle\Entity\WeezeventApiLog $weezeventApiLog = null)
    {
        $this->weezeventApiLog = $weezeventApiLog;

        return $this;
    }

    /**
     * Get weezeventApiLog
     *
     * @return \FfjvBoBundle\Entity\WeezeventApiLog
     */
    public function getWeezeventApiLog()
    {
        return $this->weezeventApiLog;
    }
}
