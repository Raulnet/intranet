<?php

namespace Ffjv\BoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserHasTeams
 *
 * @ORM\Table(name="t_user_has_teams", indexes={@ORM\Index(name="fk_teams_has_user_user1_idx", columns={"user_id"}), @ORM\Index(name="fk_teams_has_user_teams1_idx", columns={"teams_id"})})
 * @ORM\Entity(repositoryClass="Ffjv\BoBundle\Repository\UserHasTeamsRepository")
 */
class UserHasTeams
{
    /**
     * @var array
     *
     * @ORM\Column(name="uht_roles", type="array", nullable=false)
     */
    private $roles;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="uht_creation_date", type="datetime", nullable=false)
     */
    private $creationDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="uht_last_update", type="datetime", nullable=false)
     */
    private $lastUpdate;

    /**
     * @var integer
     *
     * @ORM\Column(name="uht_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Ffjv\BoBundle\Entity\TTeams
     *
     * @ORM\ManyToOne(targetEntity="Ffjv\BoBundle\Entity\Teams")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="teams_id", referencedColumnName="tea_id")
     * })
     */
    private $team;

    /**
     * @var \Ffjv\BoBundle\Entity\TUser
     *
     * @ORM\ManyToOne(targetEntity="Ffjv\BoBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="usr_id")
     * })
     */
    private $user;



    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return UserHasTeams
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
     * @return UserHasTeams
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
     * @return UserHasTeams
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
     * Set team
     *
     * @param \Ffjv\BoBundle\Entity\Teams $team
     *
     * @return UserHasTeams
     */
    public function setTeam(\Ffjv\BoBundle\Entity\Teams $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return \Ffjv\BoBundle\Entity\Teams
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set user
     *
     * @param \Ffjv\BoBundle\Entity\User $user
     *
     * @return UserHasTeams
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
}
