<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * festival_location
 *
 * @ORM\Table(name="festival_location")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\festival_locationRepository")
 */
class festival_location
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
     * @ORM\ManyToOne(targetEntity="IFlairSoapBundle\Entity\Partyfindercity", inversedBy="festival_location")
     * @ORM\JoinColumn(name = "city_id", referencedColumnName = "id")
     */
    private $cityId;

    /**
     * @var string
     *
     * @ORM\Column(name="housenumber", type="string", length=255, nullable=true)
     */
    private $housenumber;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=255, nullable=true)
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(name="zipcode", type="string", length=255, nullable=true)
     */
    private $zipcode;

    /**
     * @var string
     *
     * @ORM\Column(name="fes_latitude", type="string", length=255, nullable=true)
     */
    private $fesLatitude;

    /**
     * @var string
     *
     * @ORM\Column(name="fes_longitude", type="string", length=255, nullable=true)
     */
    private $fesLongitude;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fesLatitude
     *
     * @param string $fesLatitude
     *
     * @return festival_location
     */
    public function setFesLatitude($fesLatitude)
    {
        $this->fesLatitude = $fesLatitude;

        return $this;
    }

    /**
     * Get fesLatitude
     *
     * @return string
     */
    public function getFesLatitude()
    {
        return $this->fesLatitude;
    }

    /**
     * Set fesLongitude
     *
     * @param string $fesLongitude
     *
     * @return festival_location
     */
    public function setFesLongitude($fesLongitude)
    {
        $this->fesLongitude = $fesLongitude;

        return $this;
    }

    /**
     * Get fesLongitude
     *
     * @return string
     */
    public function getFesLongitude()
    {
        return $this->fesLongitude;
    }

    /**
     * Set cityId
     *
     * @param \IFlairSoapBundle\Entity\Partyfindercity $cityId
     *
     * @return festival_location
     */
    public function setCityId(\IFlairSoapBundle\Entity\Partyfindercity $cityId = null)
    {
        $this->cityId = $cityId;

        return $this;
    }

    /**
     * Get cityId
     *
     * @return \IFlairSoapBundle\Entity\Partyfindercity
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * Set housenumber.
     *
     * @param string $housenumber
     *
     * @return festival_location
     */
    public function setHousenumber($housenumber)
    {
        $this->housenumber = $housenumber;

        return $this;
    }

    /**
     * Get housenumber.
     *
     * @return string
     */
    public function getHousenumber()
    {
        return $this->housenumber;
    }

    /**
     * Set street.
     *
     * @param string $street
     *
     * @return festival_location
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street.
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set zipcode.
     *
     * @param string $zipcode
     *
     * @return festival_location
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode.
     *
     * @return string
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }
}
