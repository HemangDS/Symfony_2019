<?php

namespace Galop\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdvertisementZone
 *
 * @ORM\Table(name="advertisement_zone")
 * @ORM\Entity(repositoryClass="Galop\AdminBundle\Repository\AdvertisementZoneRepository")
 */
class AdvertisementZone
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
     * @ORM\Column(name="zone", type="string", length=255, nullable=true)
     */
    protected $zone;

    /**
     * @ORM\OneToMany(targetEntity="Galop\AdminBundle\Entity\Advertisement", mappedBy="zone", cascade={"remove"})
     */
    protected $advertisementone;

    
    public function __toString() 
    {
        return $this->getZone();
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
     * Set zone.
     *
     * @param string|null $zone
     *
     * @return AdvertisementZone
     */
    public function setZone($zone)
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * Get zone.
     *
     * @return string|null
     */
    public function getZone()
    {
        return $this->zone;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->advertisementone = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add advertisementone.
     *
     * @param \Galop\AdminBundle\Entity\Advertisement $advertisementone
     *
     * @return AdvertisementZone
     */
    public function addAdvertisementone(\Galop\AdminBundle\Entity\Advertisement $advertisementone)
    {
        $this->advertisementone[] = $advertisementone;

        return $this;
    }

    /**
     * Remove advertisementone.
     *
     * @param \Galop\AdminBundle\Entity\Advertisement $advertisementone
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAdvertisementone(\Galop\AdminBundle\Entity\Advertisement $advertisementone)
    {
        return $this->advertisementone->removeElement($advertisementone);
    }

    /**
     * Get advertisementone.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdvertisementone()
    {
        return $this->advertisementone;
    }
}
