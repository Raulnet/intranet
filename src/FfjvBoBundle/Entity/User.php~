<?php

namespace FfjvBoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @ORM\Table(name="t_user", uniqueConstraints={@ORM\UniqueConstraint(name="email_UNIQUE", columns={"usr_email"}), @ORM\UniqueConstraint(name="username_UNIQUE", columns={"usr_username"}), @ORM\UniqueConstraint(name="licence_id_UNIQUE", columns={"licence_id"})}, indexes={@ORM\Index(name="fk_t_user_t_user1_idx", columns={"author_user_id"})})
 * @ORM\Entity(repositoryClass="FfjvBoBundle\Repository\UserRepository")
 * @UniqueEntity("email", message="Cet email est déjà utilisé ?!")
 * @UniqueEntity("username", message="Ce Username est déjà utilisé ?!")
 */
class User implements UserInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="usr_username", type="string", length=100, nullable=false, unique=true)
     */
    private $username;

    /**
     * @var string
     * @Assert\Email()
     * @ORM\Column(name="usr_email", type="string", length=100, nullable=false, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="usr_password", type="string", length=100, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="usr_salt", type="string", length=100, nullable=false)
     */
    private $salt;

    /**
     * @var array
     *
     * @ORM\Column(name="usr_roles", type="array", nullable=false)
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="usr_nationality", type="string", length=2, nullable=false)
     */
    private $nationality = 'FR';

    /**
     * @var string
     *
     * @ORM\Column(name="usr_first_name", type="string", length=50, nullable=false)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="usr_last_name", type="string", length=50, nullable=false)
     */
    private $lastName;

    /**
     * @var integer
     *
     * @ORM\Column(name="usr_tel_mobile", type="string", length=20, nullable=true)
     */
    private $telMobile;

    /**
     * @var integer
     *
     * @ORM\Column(name="usr_tel_fix", type="string", length=20, nullable=true)
     */
    private $telFix;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="usr_birthday", type="date", nullable=false)
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="usr_gender", type="string", length=1, nullable=false)
     */
    private $gender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="usr_register_date", type="datetime", nullable=false)
     */
    private $registerDate;

    /**
     * @var string
     *
     * @ORM\Column(name="usr_activation_code", type="string", length=100, nullable=false)
     */
    private $activationCode;

    /**
     * @var string
     *
     * @ORM\Column(name="usr_address_1", type="string", length=64, nullable=false)
     */
    private $address1;

    /**
     * @var string
     *
     * @ORM\Column(name="usr_address_2", type="string", length=64, nullable=true)
     */
    private $address2;

    /**
     * @var string
     *
     * @ORM\Column(name="usr_zip_code", type="string", length=12, nullable=false)
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="usr_id_zip_code", type="string", length=3, nullable=true)
     */
    private $idZipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="usr_city", type="string", length=64, nullable=false)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="usr_country_address", type="string", length=2, nullable=false)
     */
    private $countryAddress = 'FR';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="usr_last_update", type="datetime", nullable=false)
     */
    private $lastUpdate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="usr_status", type="boolean", nullable=false)
     */
    private $status = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="usr_cgu", type="boolean", nullable=false)
     */
    private $cgu = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="usr_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \FfjvBoBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="FfjvBoBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="author_user_id", referencedColumnName="usr_id")
     * })
     */
    private $authorUser;

    /**
     * @var \FfjvBoBundle\Entity\LicencesUsers
     *
     * @ORM\ManyToOne(targetEntity="FfjvBoBundle\Entity\LicencesUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="licence_id", referencedColumnName="lic_id")
     * })
     */
    private $licence;

    /**
     * @var ArrayCollection $clubs
     *
     * @ORM\OneToMany(targetEntity="FfjvBoBundle\Entity\Clubs", mappedBy="user")
     */
    private $clubs;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->roles          = array("ROLE_USER");
        $this->salt           = base_convert(sha1(md5(uniqid(mt_rand(), true))), 16, 36);
        $this->activationCode = base_convert(md5(uniqid(mt_rand(), true)), 16, 36);
        $this->lastUpdate     = new \DateTime('now');
        $this->registerDate   = new \DateTime('now');
        $this->birthday       = new \DateTime('1980-01-01 00:00:00');
        $this->clubs          = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->username;
    }

    /**
     * eraseCredentials
     */
    public function eraseCredentials()
    {
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * @param string $nationality
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return int
     */
    public function getTelMobile()
    {
        return $this->telMobile;
    }

    /**
     * @param int $telMobile
     */
    public function setTelMobile($telMobile)
    {
        $this->telMobile = $telMobile;
    }

    /**
     * @return int
     */
    public function getTelFix()
    {
        return $this->telFix;
    }

    /**
     * @param int $telFix
     */
    public function setTelFix($telFix)
    {
        $this->telFix = $telFix;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return \DateTime
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }

    /**
     * @param \DateTime $registerDate
     */
    public function setRegisterDate($registerDate)
    {
        $this->registerDate = $registerDate;
    }

    /**
     * @return string
     */
    public function getActivationCode()
    {
        return $this->activationCode;
    }

    /**
     * @param string $activationCode
     */
    public function setActivationCode($activationCode)
    {
        $this->activationCode = $activationCode;
    }

    /**
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * @param string $address1
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
    }

    /**
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @param string $address2
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param string $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return string
     */
    public function getIdZipCode()
    {
        return $this->idZipCode;
    }

    /**
     * @param string $idZipCode
     */
    public function setIdZipCode($idZipCode)
    {
        $this->idZipCode = $idZipCode;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCountryAddress()
    {
        return $this->countryAddress;
    }

    /**
     * @param string $countryAddress
     */
    public function setCountryAddress($countryAddress)
    {
        $this->countryAddress = $countryAddress;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * @param \DateTime $lastUpdate
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;
    }

    /**
     * @return boolean
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return boolean
     */
    public function isCgu()
    {
        return $this->cgu;
    }

    /**
     * @param boolean $cgu
     */
    public function setCgu($cgu)
    {
        $this->cgu = $cgu;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getAuthorUser()
    {
        return $this->authorUser;
    }

    /**
     * @param User $authorUser
     */
    public function setAuthorUser($authorUser)
    {
        $this->authorUser = $authorUser;
    }

    /**
     * @return LicencesUsers
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * @param LicencesUsers $licence
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;
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
     * Get cgu
     *
     * @return boolean
     */
    public function getCgu()
    {
        return $this->cgu;
    }

    /**
     * Add club
     *
     * @param \FfjvBoBundle\Entity\Clubs $club
     *
     * @return User
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
