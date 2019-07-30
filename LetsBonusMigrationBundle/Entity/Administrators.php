<?php

namespace iFlair\LetsBonusMigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Administrators.
 *
 * @ORM\Table(name="administrators", uniqueConstraints={@ORM\UniqueConstraint(name="n2_administrators", columns={"email"})}, indexes={@ORM\Index(name="n1_administrators", columns={"group_id"})})
 * @ORM\Entity
 */
class Administrators
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
     * @ORM\Column(name="username", type="string", length=255, nullable=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=128, nullable=true)
     */
    private $password;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    private $enabled = '1';

    /**
     * @var int
     *
     * @ORM\Column(name="group_id", type="integer", nullable=true)
     */
    private $groupId;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastLogin", type="datetime", nullable=false)
     */
    private $lastlogin = '0000-00-00 00:00:00';

    /**
     * @var int
     *
     * @ORM\Column(name="lastCompanyid", type="integer", nullable=true)
     */
    private $lastcompanyid;

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
     * Set username.
     *
     * @param string $username
     *
     * @return Administrators
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return Administrators
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return Administrators
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
     * Set groupId.
     *
     * @param int $groupId
     *
     * @return Administrators
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId.
     *
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Administrators
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
     * Set lastlogin.
     *
     * @param \DateTime $lastlogin
     *
     * @return Administrators
     */
    public function setLastlogin($lastlogin)
    {
        $this->lastlogin = $lastlogin;

        return $this;
    }

    /**
     * Get lastlogin.
     *
     * @return \DateTime
     */
    public function getLastlogin()
    {
        return $this->lastlogin;
    }

    /**
     * Set lastcompanyid.
     *
     * @param int $lastcompanyid
     *
     * @return Administrators
     */
    public function setLastcompanyid($lastcompanyid)
    {
        $this->lastcompanyid = $lastcompanyid;

        return $this;
    }

    /**
     * Get lastcompanyid.
     *
     * @return int
     */
    public function getLastcompanyid()
    {
        return $this->lastcompanyid;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Administrators
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
     * @return Administrators
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
