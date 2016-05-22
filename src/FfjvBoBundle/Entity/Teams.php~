<?php

namespace FfjvBoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Teams
 *
 * @ORM\Table(name="t_teams", uniqueConstraints={@ORM\UniqueConstraint(name="licence_id_UNIQUE", columns={"licence_id"})}, indexes={@ORM\Index(name="fk_teams_club1_idx", columns={"clubs_id"}), @ORM\Index(name="fk_teams_user1_idx", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="FfjvBoBundle\Repository\TeamsRepository")
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
     * @var \FfjvBoBundle\Entity\LicencesTeams
     *
     * @ORM\ManyToOne(targetEntity="FfjvBoBundle\Entity\LicencesTeams")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="licence_id", referencedColumnName="lic_id")
     * })
     */
    private $licence;

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
     * @ORM\ManyToOne(targetEntity="FfjvBoBundle\Entity\Clubs", inversedBy="teams")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="clubs_id", referencedColumnName="clu_id")
     * })
     */
    private $club;

    /**
     * @var ArrayCollection $members
     *
     * @ORM\OneToMany(targetEntity="FfjvBoBundle\Entity\UserHasTeams", mappedBy="team", cascade={"persist", "remove"})
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
     * @param \FfjvBoBundle\Entity\LicencesTeams $licence
     *
     * @return Teams
     */
    public function setLicence(\FfjvBoBundle\Entity\LicencesTeams $licence = null)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get licence
     *
     * @return \FfjvBoBundle\Entity\LicencesTeams
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
     * @return Teams
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
     * @return Teams
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
     * Add member
     *
     * @param \FfjvBoBundle\Entity\UserHasTeams $member
     *
     * @return Teams
     */
    public function addMember(\FfjvBoBundle\Entity\UserHasTeams $member)
    {
        $this->members[] = $member;

        return $this;
    }

    /**
     * Remove member
     *
     * @param \FfjvBoBundle\Entity\UserHasTeams $member
     */
    public function removeMember(\FfjvBoBundle\Entity\UserHasTeams $member)
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
