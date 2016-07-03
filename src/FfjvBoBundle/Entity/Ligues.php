<?php

namespace FfjvBoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Ligues
 *
 * @ORM\Table(name="t_ligues", uniqueConstraints={@ORM\UniqueConstraint(name="title_UNIQUE", columns={"lig_title"})}, indexes={@ORM\Index(name="fk_ligues_user1_idx", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="FfjvBoBundle\Repository\LiguesRepository")
 * @UniqueEntity("title")
 * @UniqueEntity("tag")
 * @UniqueEntity("rna")
 * @UniqueEntity("email")
 */
class Ligues
{
    /**
     * @var string
     *
     * @ORM\Column(name="lig_title", type="string", length=80, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="lig_tag", type="string", length=10, nullable=false)
     */
    private $tag;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lig_creation_date", type="datetime", nullable=false)
     */
    private $creationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="lig_rna", type="string", length=10, nullable=true)
     */
    private $rna;

    /**
     * @var integer
     *
     * @ORM\Column(name="lig_siren", type="integer", nullable=true)
     */
    private $siren;

    /**
     * @var integer
     *
     * @ORM\Column(name="lig_siret", type="integer", nullable=true)
     */
    private $siret;

    /**
     * @var string
     *
     * @ORM\Column(name="lig_ape", type="string", length=5, nullable=true)
     */
    private $ape;

    /**
     * @var string
     * @Assert\Email()
     *
     * @ORM\Column(name="lig_email", type="string", length=100, nullable=false)
     */
    private $email;

    /**
     * @var integer
     *
     * @ORM\Column(name="lig_tel_fix", type="string", length=20, nullable=true)
     */
    private $telFix;

    /**
     * @var integer
     *
     * @ORM\Column(name="lig_tel_mobile", type="string", length=20, nullable=true)
     */
    private $telMobile;

    /**
     * @var integer
     *
     * @ORM\Column(name="lig_tel_mobile", type="integer", nullable=true)
     */

    /**
     * @var string
     *
     * @ORM\Column(name="lig_address_1", type="string", length=100, nullable=true)
     */
    private $address1;

    /**
     * @var string
     *
     * @ORM\Column(name="lig_address_2", type="string", length=100, nullable=true)
     */
    private $address2;

    /**
     * @var string
     *
     * @ORM\Column(name="lig_zip_code", type="string", length=12, nullable=true)
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="lig_city", type="string", length=64, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="lig_about", type="text", nullable=true)
     */
    private $about;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lig_last_update", type="datetime", nullable=false)
     */
    private $lastUpdate;

    /**
     * @var integer
     *
     * @ORM\Column(name="lig_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \FfjvBoBundle\Entity\TUser
     *
     * @ORM\ManyToOne(targetEntity="FfjvBoBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="usr_id")
     * })
     */
    private $user;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="FfjvBoBundle\Entity\Clubs", mappedBy="ligues")
     */
    private $clubs;

    /**
     * Ligues constructor.
     */
    public function __construct()
    {
        $this->clubs = new ArrayCollection();
        $this->creationDate = new \DateTime('now');
        $this->lastUpdate   = new \DateTime('now');
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
     * @return Ligues
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
     * @return Ligues
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
     * @return Ligues
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
     * @return Ligues
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
     * @return Ligues
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
     * @return Ligues
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
     * @return Ligues
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
     * @return Ligues
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
     * @return Ligues
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
     * @return Ligues
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
     * Set address1
     *
     * @param string $address1
     *
     * @return Ligues
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
     * @return Ligues
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
     * @return Ligues
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
     * Set city
     *
     * @param string $city
     *
     * @return Ligues
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
     * @return Ligues
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
     * @return Ligues
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
     * @return Ligues
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
     * Add club
     *
     * @param \FfjvBoBundle\Entity\Clubs $club
     *
     * @return Ligues
     */
    public function addClub(\FfjvBoBundle\Entity\Clubs $club)
    {
        $this->clubs[] = $club;

        return $this;
    }

    /**
     * Remove club
     *
     * @param \FfjvBoBundle\Entity\Clubs $club
     */
    public function removeClub(\FfjvBoBundle\Entity\Clubs $club)
    {
        $this->clubs->removeElement($club);
    }

    /**
     * Get clubs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClubs()
    {
        return $this->clubs;
    }



}
