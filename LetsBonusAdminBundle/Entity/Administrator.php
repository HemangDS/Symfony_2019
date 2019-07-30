<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Administrator.
 *
 * @ORM\Table(name="lb_administrators")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\AdministratorRepository")
 */
class Administrator
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     */
    private $password = null;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Groups", inversedBy="administrator")
     * @ORM\JoinColumn(name="groups", referencedColumnName="id")
     */
    private $groups;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastLogin", type="datetime", nullable=true)
     */
    private $lastLogin = null;

    /**
     * @var int
     *
     * @ORM\Column(name="lastCompanyId", type="integer", nullable=true)
     */
    private $lastCompanyId = null;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime")
     */
    private $modified;

    public function __toString()
    {
        return strval($this->id);
    }

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

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
     * Set id.
     *
     * @param int $id
     *
     * @return Administrator
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return Administrator
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
     * @return Administrator
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
     * Set status.
     *
     * @param int $status
     *
     * @return Administrator
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set groups.
     *
     * @param int $groups
     *
     * @return Administrator
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Get groups.
     *
     * @return int
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Administrator
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
     * Set lastLogin.
     *
     * @param \DateTime $lastLogin
     *
     * @return Administrator
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin.
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set lastCompanyId.
     *
     * @param int $lastCompanyId
     *
     * @return Administrator
     */
    public function setLastCompanyId($lastCompanyId)
    {
        $this->lastCompanyId = $lastCompanyId;

        return $this;
    }

    /**
     * Get lastCompanyId.
     *
     * @return int
     */
    public function getLastCompanyId()
    {
        return $this->lastCompanyId;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Collection
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
     * @return Collection
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
