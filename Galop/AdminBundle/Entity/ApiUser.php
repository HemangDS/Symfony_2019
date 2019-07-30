<?php

namespace Galop\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Vivait\StringGeneratorBundle\Annotation\GeneratorAnnotation as Generate;

/**
 * ApiUser
 *
 * @ORM\Table(name="api_user")
 * @ORM\Entity(repositoryClass="Galop\AdminBundle\Repository\ApiUserRepository")
 */
class ApiUser
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
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    protected $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     */
    protected $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="usertoken", type="string", length=255, nullable=true)
     * @Generate(generator="secure_string", options={"length"=52, "chars"="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", "strength"="medium"})
     */
    protected $UserToken;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     *
     * @ORM\Column(type="boolean", name="enabled")
     */
    private $enabled;

    /**
     * @ORM\OneToMany(targetEntity="ApiArticle", mappedBy="ApiuserId", cascade={"remove"})
     */
    private $apiusers;

    public function __toString(){
        return $this->firstname;    
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
     * Set firstname.
     *
     * @param string|null $firstname
     *
     * @return ApiUser
     */
    public function setFirstname($firstname = null)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname.
     *
     * @return string|null
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname.
     *
     * @param string|null $lastname
     *
     * @return ApiUser
     */
    public function setLastname($lastname = null)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname.
     *
     * @return string|null
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return ApiUser
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return ApiUser
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return ApiUser
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
     * Set userToken.
     *
     * @param string|null $userToken
     *
     * @return ApiUser
     */
    public function setUserToken($userToken = null)
    {
        $this->UserToken = $userToken;

        return $this;
    }

    /**
     * Get userToken.
     *
     * @return string|null
     */
    public function getUserToken()
    {
        return $this->UserToken;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->apiusers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add apiuser.
     *
     * @param \Galop\AdminBundle\Entity\ApiArticle $apiuser
     *
     * @return ApiUser
     */
    public function addApiuser(\Galop\AdminBundle\Entity\ApiArticle $apiuser)
    {
        $this->apiusers[] = $apiuser;

        return $this;
    }

    /**
     * Remove apiuser.
     *
     * @param \Galop\AdminBundle\Entity\ApiArticle $apiuser
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeApiuser(\Galop\AdminBundle\Entity\ApiArticle $apiuser)
    {
        return $this->apiusers->removeElement($apiuser);
    }

    /**
     * Get apiusers.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApiusers()
    {
        return $this->apiusers;
    }
}
