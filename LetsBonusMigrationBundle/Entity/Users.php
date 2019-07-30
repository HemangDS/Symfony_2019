<?php

namespace iFlair\LetsBonusMigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users.
 *
 * @ORM\Table(name="users", indexes={@ORM\Index(name="n1_users", columns={"company_id"})})
 * @ORM\Entity
 */
class Users
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255, nullable=true)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    private $enabled = '1';

    /**
     * @var int
     *
     * @ORM\Column(name="company_id", type="integer", nullable=true)
     */
    private $companyId = '1';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="userCreateDate", type="datetime", nullable=true)
     */
    private $usercreatedate;

    /**
     * @var string
     *
     * @ORM\Column(name="userType", type="string", length=100, nullable=true)
     */
    private $usertype;

    /**
     * @var bool
     *
     * @ORM\Column(name="userGender", type="boolean", nullable=true)
     */
    private $usergender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="userBirthDate", type="date", nullable=true)
     */
    private $userbirthdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    private $modified;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Users
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname.
     *
     * @param string $surname
     *
     * @return Users
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname.
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Users
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return Users
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set companyId.
     *
     * @param int $companyId
     *
     * @return Users
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;

        return $this;
    }

    /**
     * Get companyId.
     *
     * @return int
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Set usercreatedate.
     *
     * @param \DateTime $usercreatedate
     *
     * @return Users
     */
    public function setUsercreatedate($usercreatedate)
    {
        $this->usercreatedate = $usercreatedate;

        return $this;
    }

    /**
     * Get usercreatedate.
     *
     * @return \DateTime
     */
    public function getUsercreatedate()
    {
        return $this->usercreatedate;
    }

    /**
     * Set usertype.
     *
     * @param string $usertype
     *
     * @return Users
     */
    public function setUsertype($usertype)
    {
        $this->usertype = $usertype;

        return $this;
    }

    /**
     * Get usertype.
     *
     * @return string
     */
    public function getUsertype()
    {
        return $this->usertype;
    }

    /**
     * Set usergender.
     *
     * @param bool $usergender
     *
     * @return Users
     */
    public function setUsergender($usergender)
    {
        $this->usergender = $usergender;

        return $this;
    }

    /**
     * Get usergender.
     *
     * @return bool
     */
    public function getUsergender()
    {
        return $this->usergender;
    }

    /**
     * Set userbirthdate.
     *
     * @param \DateTime $userbirthdate
     *
     * @return Users
     */
    public function setUserbirthdate($userbirthdate)
    {
        $this->userbirthdate = $userbirthdate;

        return $this;
    }

    /**
     * Get userbirthdate.
     *
     * @return \DateTime
     */
    public function getUserbirthdate()
    {
        return $this->userbirthdate;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Users
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified.
     *
     * @param \DateTime $modified
     *
     * @return Users
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified.
     *
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }
}
