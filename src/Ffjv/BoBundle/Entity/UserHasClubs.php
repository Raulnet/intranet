<?php

namespace Ffjv\BoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserHasClubs
 *
 * @ORM\Table(name="t_user_has_clubs", indexes={@ORM\Index(name="fk_clubs_has_users_user1_idx", columns={"user_id"}), @ORM\Index(name="fk_clubs_has_users_clubs1_idx", columns={"clubs_id"})})
 * @ORM\Entity(repositoryClass="Ffjv\BoBundle\Repository\UserHasClubsRepository")
 */
class UserHasClubs
{
    /**
     * @var array
     *
     * @ORM\Column(name="uhc_roles", type="array", nullable=false)
     */
    private $roles;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="uhc_creation_date", type="datetime", nullable=false)
     */
    private $creationDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="uhc_last_update", type="datetime", nullable=false)
     */
    private $lastUpdate;

    /**
     * @var integer
     *
     * @ORM\Column(name="uhc_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Ffjv\BoBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Ffjv\BoBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="usr_id")
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
     * UserHasClubs constructor.
     */
    public function __construct()
    {
        $this->creationDate = new \DateTime('now');
        $this->lastUpdate   = new \DateTime('now');
        $this->roles        = array("ROLE_USER");
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return UserHasClubs
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return UserHasClubs
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
     * @return UserHasClubs
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
     * @param \Ffjv\BoBundle\Entity\User $user
     *
     * @return UserHasClubs
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
     * @return UserHasClubs
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
}