<?php

namespace FfjvBoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LicencesTeams
 *
 * @ORM\Table(name="t_licences_teams", uniqueConstraints={@ORM\UniqueConstraint(name="lic_licence_UNIQUE", columns={"lic_licence"})})
 * @ORM\Entity(repositoryClass="FfjvBoBundle\Repository\LicencesTeamsRepository")
 */
class LicencesTeams
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lic_register_date", type="datetime", nullable=false)
     */
    private $registerDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lic_activation_date", type="datetime", nullable=false)
     */
    private $activationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="lic_licence", type="string", length=50, nullable=true)
     */
    private $licence;

    /**
     * @var integer
     *
     * @ORM\Column(name="lic_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * LicencesTeams constructor.
     */
    public function __construct()
    {
        $this->registerDate = new \DateTime('now');
        $this->activationDate = new \DateTime('now');
    }
    /**
     * @return string
     */
    function __toString()
    {
        return $this->licence;
    }


    /**
     * Set registerDate
     *
     * @param \DateTime $registerDate
     *
     * @return LicencesTeams
     */
    public function setRegisterDate($registerDate)
    {
        $this->registerDate = $registerDate;

        return $this;
    }

    /**
     * Get registerDate
     *
     * @return \DateTime
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }

    /**
     * Set activationDate
     *
     * @param \DateTime $activationDate
     *
     * @return LicencesTeams
     */
    public function setActivationDate($activationDate)
    {
        $this->activationDate = $activationDate;

        return $this;
    }

    /**
     * Get activationDate
     *
     * @return \DateTime
     */
    public function getActivationDate()
    {
        return $this->activationDate;
    }

    /**
     * Set licence
     *
     * @param string $licence
     *
     * @return LicencesTeams
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get licence
     *
     * @return string
     */
    public function getLicence()
    {
        return $this->licence;
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
}
