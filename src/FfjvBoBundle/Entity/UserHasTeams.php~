<?php

namespace FfjvBoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserHasTeams
 *
 * @ORM\Table(name="t_user_has_teams", indexes={@ORM\Index(name="fk_teams_has_user_user1_idx", columns={"user_id"}), @ORM\Index(name="fk_teams_has_user_teams1_idx", columns={"teams_id"})})
 * @ORM\Entity(repositoryClass="FfjvBoBundle\Repository\UserHasTeamsRepository")
 */
class UserHasTeams
{
    const ROLE_TEAM_MEMBER = 'ROLE_TEAM_MEMBER';
    const ROLE_TEAM_SUB_MEMBER = 'ROLE_TEAM_SUB_MEMBER';
    const ROLE_TEAM_LEADER = 'ROLE_TEAM_LEADER';
    const ROLE_TEAM_SUB_LEADER = 'ROLE_TEAM_SUB_LEADER';

    /**
     * @var array
     */
    public static $listRoles = [
        'membre' =>  self::ROLE_TEAM_MEMBER,
        'remplaçant' => self::ROLE_TEAM_SUB_MEMBER,
        'leader' => self::ROLE_TEAM_LEADER,
        'second' => self::ROLE_TEAM_SUB_LEADER];

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
     * @var \FfjvBoBundle\Entity\TTeams
     *
     * @ORM\ManyToOne(targetEntity="FfjvBoBundle\Entity\Teams", inversedBy="members")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="teams_id", referencedColumnName="tea_id")
     * })
     */
    private $team;

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
     * UserHasTeams constructor.
     */
    public function __construct()
    {
        $this->creationDate = new \DateTime('now');
        $this->lastUpdate = new \DateTime('now');
        $this->roles = [self::ROLE_TEAM_MEMBER];
    }


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
     * @param \FfjvBoBundle\Entity\Teams $team
     *
     * @return UserHasTeams
     */
    public function setTeam(\FfjvBoBundle\Entity\Teams $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return \FfjvBoBundle\Entity\Teams
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set user
     *
     * @param \FfjvBoBundle\Entity\User $user
     *
     * @return UserHasTeams
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
