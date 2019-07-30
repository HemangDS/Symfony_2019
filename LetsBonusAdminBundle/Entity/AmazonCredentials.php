<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AmazonCredentials.
 *
 * @ORM\Table(name="lb_amazon_credentials")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\AmazonCredentialsRepository")
 */
class AmazonCredentials
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
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

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

    /**
     * @var networkCredentials
     * @ORM\ManyToOne(targetEntity="networkCredentials" , inversedBy="amazonCredentials")
     * @ORM\JoinColumn(name="lb_network_credentials_id", referencedColumnName="id", nullable=FALSE)
     */
    private $networkCredentials;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    public function __toString()
    {
        return strval($this->id);
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set username.
     *
     * @param string $username
     *
     * @return AmazonCredentials
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
     * @return AmazonCredentials
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
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return AmazonCredentials
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
     * @return AmazonCredentials
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

    public function setNetworkCredentials(networkCredentials $networkCredentials = null)
    {
        $this->networkCredentials = $networkCredentials;

        return $this;
    }

    public function getNetworkCredentials()
    {
        return $this->networkCredentials;
    }
}
