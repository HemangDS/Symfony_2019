<?php

namespace Galop\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * SubscriptionPackage
 *
 * @ORM\Table(name="subscription_package")
 * @ORM\Entity(repositoryClass="Galop\AdminBundle\Repository\SubscriptionPackageRepository")
 */
class SubscriptionPackage
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

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
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    protected $country;

    /**
     * @var string
     *
     * @ORM\Column(name="packagestatus", type="string", length=255, nullable=true)
     */
    protected $packagestatus;

    /**
     * @var string
     *
     * @ORM\Column(name="timeperiod", type="string", length=255, nullable=true)
     */
    protected $timeperiod;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    protected $price;

    /**
     * @var string
     *
     * @ORM\Column(name="vat", type="string", length=255, nullable=true)
     */
    protected $vat;

    /**
     * @var string
     *
     * @ORM\Column(name="totalprice", type="string", length=255, nullable=true)
     */
    protected $totalprice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    public function __toString(){
        return $this->title;    
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
     * Set title.
     *
     * @param string|null $title
     *
     * @return SubscriptionPackage
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return SubscriptionPackage
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
     * @return SubscriptionPackage
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
     * Set country.
     *
     * @param string|null $country
     *
     * @return SubscriptionPackage
     */
    public function setCountry($country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country.
     *
     * @return string|null
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set packagestatus.
     *
     * @param string|null $packagestatus
     *
     * @return SubscriptionPackage
     */
    public function setPackagestatus($packagestatus = null)
    {
        $this->packagestatus = $packagestatus;

        return $this;
    }

    /**
     * Get packagestatus.
     *
     * @return string|null
     */
    public function getPackagestatus()
    {
        return $this->packagestatus;
    }

    /**
     * Set timeperiod.
     *
     * @param string|null $timeperiod
     *
     * @return SubscriptionPackage
     */
    public function setTimeperiod($timeperiod = null)
    {
        $this->timeperiod = $timeperiod;

        return $this;
    }

    /**
     * Get timeperiod.
     *
     * @return string|null
     */
    public function getTimeperiod()
    {
        return $this->timeperiod;
    }

    /**
     * Set price.
     *
     * @param string|null $price
     *
     * @return SubscriptionPackage
     */
    public function setPrice($price = null)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return string|null
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set vat.
     *
     * @param string|null $vat
     *
     * @return SubscriptionPackage
     */
    public function setVat($vat = null)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Get vat.
     *
     * @return string|null
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * Set totalprice.
     *
     * @param string|null $totalprice
     *
     * @return SubscriptionPackage
     */
    public function setTotalprice($totalprice = null)
    {
        $this->totalprice = $totalprice;

        return $this;
    }

    /**
     * Get totalprice.
     *
     * @return string|null
     */
    public function getTotalprice()
    {
        return $this->totalprice;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return SubscriptionPackage
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }
}
