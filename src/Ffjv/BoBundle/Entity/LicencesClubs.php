<?php

namespace Ffjv\BoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LicencesClubs
 *
 * @ORM\Table(name="t_licences_clubs", uniqueConstraints={@ORM\UniqueConstraint(name="lic_licence_UNIQUE", columns={"lic_licence"})})
 * @ORM\Entity(repositoryClass="Ffjv\BoBundle\Repository\LicencesClubsRepository")
 */
class LicencesClubs
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
     * LicencesClubs constructor.
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
     * @return LicencesClubs
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
     * @return LicencesClubs
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
     * @return LicencesClubs
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
