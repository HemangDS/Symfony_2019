<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Country.
 *
 * @ORM\Table(name="lb_country")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\CountryRepository")
 */
class Country
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

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
     * @ORM\ManyToMany(targetEntity="Network", mappedBy="country")
     */
    private $network;

    /**
     * @ORM\ManyToOne(targetEntity="Language", inversedBy="country")
     */
    private $language;

    /**
     * @ORM\ManyToMany(targetEntity="Domain", inversedBy="country")
     * @ORM\JoinTable(name="lb_country_domain",
     *      joinColumns={@ORM\JoinColumn(name="country_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="domain_id", referencedColumnName="id")}
     *      )
     */
    private $domain;

    public function __construct()
    {
        $this->domain = new ArrayCollection();
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
        $this->network = new ArrayCollection();
    }

    public function getNetwork()
    {
        return $this->network;
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
     * Set name.
     *
     * @param string $name
     *
     * @return Country
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
     * Set code.
     *
     * @param string $code
     *
     * @return Country
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Country
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
     * @return Country
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

    public function setLanguage(Language $language)
    {
        $this->language = $language;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function addDomain(Domain $domain)
    {
        $this->domain[] = $domain;

        return $this;
    }

    public function removeDomain(Domain $domain)
    {
        $this->domain->removeElement($domain);
    }

    public function setDomain(Domain $domain)
    {
        $this->domain = $domain;
    }

    public function getDomain()
    {
        return $this->domain;
    }
}
