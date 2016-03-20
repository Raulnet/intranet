<?php

namespace Ffjv\BoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Teams
 *
 * @ORM\Table(name="t_teams", uniqueConstraints={@ORM\UniqueConstraint(name="licence_id_UNIQUE", columns={"licence_id"})}, indexes={@ORM\Index(name="fk_teams_club1_idx", columns={"clubs_id"}), @ORM\Index(name="fk_teams_user1_idx", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="Ffjv\BoBundle\Repository\TeamsRepository")
 */
class Teams
{
    /**
     * @var string
     *
     * @ORM\Column(name="tea_title", type="string", length=80, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="tea_tag", type="string", length=10, nullable=false)
     */
    private $tag;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tea_creation_date", type="datetime", nullable=false)
     */
    private $creationDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tea_last_update", type="datetime", nullable=false)
     */
    private $lastUpdate;

    /**
     * @var integer
     *
     * @ORM\Column(name="tea_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Ffjv\BoBundle\Entity\LicencesTeams
     *
     * @ORM\ManyToOne(targetEntity="Ffjv\BoBundle\Entity\LicencesTeams")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="licence_id", referencedColumnName="lic_id")
     * })
     */
    private $licence;

    /**
     * @var \Ffjv\BoBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Ffjv\BoBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="usr_id", onDelete="SET NULL")
     * })
     */
    private $user;

    /**
     * @var \Ffjv\BoBundle\Entity\Clubs
     *
     * @ORM\ManyToOne(targetEntity="Ffjv\BoBundle\Entity\Clubs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="clubs_id", referencedColumnName="clu_id")
     * })
     */
    private $club;

    /**
     * @var ArrayCollection $members
     *
     * @ORM\OneToMany(targetEntity="Ffjv\BoBundle\Entity\UserHasTeams", mappedBy="team", cascade={"persist", "remove"})
     */
    private $members = array();

    /**
     * Clubs constructor.
     */
    public function __construct()
    {
        $this->creationDate = new \DateTime('now');
        $this->lastUpdate   = new \DateTime('now');
        $this->members = new ArrayCollection();
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
     * @return Teams
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
     * @return Teams
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
     * @return Teams
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
     * @return Teams
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
     * Set licence
     *
     * @param \Ffjv\BoBundle\Entity\LicencesTeams $licence
     *
     * @return Teams
     */
    public function setLicence(\Ffjv\BoBundle\Entity\LicencesTeams $licence = null)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get licence
     *
     * @return \Ffjv\BoBundle\Entity\LicencesTeams
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set user
     *
     * @param \Ffjv\BoBundle\Entity\User $user
     *
     * @return Teams
     */
    public function setUser(\Ffjv\BoBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Ffjv\BoBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set club
     *
     * @param \Ffjv\BoBundle\Entity\Clubs $club
     *
     * @return Teams
     */
    public function setClub(\Ffjv\BoBundle\Entity\Clubs $club = null)
    {
        $this->club = $club;

        return $this;
    }

    /**
     * Get club
     *
     * @return \Ffjv\BoBundle\Entity\Clubs
     */
    public function getClub()
    {
        return $this->club;
    }

    /**
     * Add member
     *
     * @param \Ffjv\BoBundle\Entity\UserHasTeams $member
     *
     * @return Teams
     */
    public function addMember(\Ffjv\BoBundle\Entity\UserHasTeams $member)
    {
        $this->members[] = $member;

        return $this;
    }

    /**
     * Remove member
     *
     * @param \Ffjv\BoBundle\Entity\UserHasTeams $member
     */
    public function removeMember(\Ffjv\BoBundle\Entity\UserHasTeams $member)
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
}
