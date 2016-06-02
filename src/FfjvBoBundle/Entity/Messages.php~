<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 05/12/15
 * Time: 23:58
 */

namespace FfjvBoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Messages
 *
 * @ORM\Table(name="t_messages")
 * @ORM\Entity(repositoryClass="FfjvBoBundle\Repository\MessagesRepository")
 */
class Messages
{

    const CONTACT_CLUB = 'CONTACT_CLUB';

    const REQUEST_JOIN_CLUB    = 'REQUEST_JOIN_CLUB';

    /**
     * @var integer
     *
     * @ORM\Column(name="mes_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="mes_type", type="string", nullable=false, length=20)
     */
    private $type = self::CONTACT_CLUB;

    /**
     * @var string
     *
     * @ORM\Column(name="mes_priority", type="integer", nullable=false)
     */
    private $priority = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="mes_subject", type="string", length=80, nullable=false)
     */
    private $subject = '';

    /**
     * @var string
     *
     * @ORM\Column(name="mes_message", type="string", nullable=false)
     */
    private $message = '';

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
     * @ORM\ManyToOne(targetEntity="FfjvBoBundle\Entity\Clubs", inversedBy="messages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="club_id", referencedColumnName="clu_id", onDelete="SET NULL")
     * })
     */
    private $club;

    /**
     * @var \FfjvBoBundle\Entity\Ligues
     *
     * @ORM\ManyToOne(targetEntity="FfjvBoBundle\Entity\Ligues")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lig_id", referencedColumnName="lig_id", onDelete="SET NULL")
     * })
     */
    private $ligue;

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
     * @var string
     *
     * @ORM\Column(name="mes_email", type="string", nullable=false)
     */
    private $email = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="mes_creation_date", type="datetime", nullable=false)
     */
    private $creationDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="mes_read", type="integer", nullable=false)
     */
    private $read = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="mes_signal", type="integer", nullable=false)
     */
    private $signal = false;

    /**
     * Messages constructor.
     */
    public function __construct()
    {
        $this->creationDate = new \DateTime('now');
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
     * Set type
     *
     * @param integer $type
     *
     * @return Messages
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     *
     * @return Messages
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return Messages
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set entity
     *
     * @param string $entity
     *
     * @return Messages
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Messages
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Messages
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
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Messages
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
     * Set read
     *
     * @param integer $read
     *
     * @return Messages
     */
    public function setRead($read)
    {
        $this->read = $read;

        return $this;
    }

    /**
     * Get read
     *
     * @return integer
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * Set signal
     *
     * @param integer $signal
     *
     * @return Messages
     */
    public function setSignal($signal)
    {
        $this->signal = $signal;

        return $this;
    }

    /**
     * Get signal
     *
     * @return integer
     */
    public function getSignal()
    {
        return $this->signal;
    }

    /**
     * Set user
     *
     * @param \FfjvBoBundle\Entity\User $user
     *
     * @return Messages
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
     * @return Messages
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
     * Set ligue
     *
     * @param \FfjvBoBundle\Entity\Ligues $ligue
     *
     * @return Messages
     */
    public function setLigue(\FfjvBoBundle\Entity\Ligues $ligue = null)
    {
        $this->ligue = $ligue;

        return $this;
    }

    /**
     * Get ligue
     *
     * @return \FfjvBoBundle\Entity\Ligues
     */
    public function getLigue()
    {
        return $this->ligue;
    }

    /**
     * Set authorUser
     *
     * @param \FfjvBoBundle\Entity\User $authorUser
     *
     * @return Messages
     */
    public function setAuthorUser(\FfjvBoBundle\Entity\User $authorUser = null)
    {
        $this->authorUser = $authorUser;

        return $this;
    }

    /**
     * Get authorUser
     *
     * @return \FfjvBoBundle\Entity\User
     */
    public function getAuthorUser()
    {
        return $this->authorUser;
    }
}
