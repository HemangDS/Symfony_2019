<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * festival_organizer
 *
 * @ORM\Table(name="festival_organizer")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\festival_organizerRepository")
 */
class festival_organizer
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
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\festival", inversedBy="festival_artist")
     * @ORM\JoinColumn(name = "festival_id", referencedColumnName = "id")
     */
    private $festivalId;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=255)
     */
    private $street;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairSoapBundle\Entity\Partyfindercity", inversedBy="Partyfindercity")
     * @ORM\JoinColumn(name = "city", referencedColumnName = "id")
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="zipcode", type="string", length=255)
     */
    private $zipcode;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairSoapBundle\Entity\Partyfindercountry", inversedBy="Partyfindercountry")
     * @ORM\JoinColumn(name = "country", referencedColumnName = "id")
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_no", type="string", length=255)
     */
    private $phoneNo;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="manager", type="string", length=255)
     */
    private $manager;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255)
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="organizer_name", type="string", length=255)
     */
    private $organizerName;

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="string", length=255)
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="string", length=255)
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="host", type="string", length=255)
     */
    private $host;


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
     * Set zipcode
     *
     * @param string $zipcode
     *
     * @return festival_organizer
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode
     *
     * @return string
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set phoneNo
     *
     * @param string $phoneNo
     *
     * @return festival_organizer
     */
    public function setPhoneNo($phoneNo)
    {
        $this->phoneNo = $phoneNo;

        return $this;
    }

    /**
     * Get phoneNo
     *
     * @return string
     */
    public function getPhoneNo()
    {
        return $this->phoneNo;
    }

    /**
     * Set website
     *
     * @param string $website
     *
     * @return festival_organizer
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set organizerName
     *
     * @param string $organizerName
     *
     * @return festival_organizer
     */
    public function setOrganizerName($organizerName)
    {
        $this->organizerName = $organizerName;

        return $this;
    }

    /**
     * Get organizerName
     *
     * @return string
     */
    public function getOrganizerName()
    {
        return $this->organizerName;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return festival_organizer
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return festival_organizer
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set street
     *
     * @param string $street
     *
     * @return festival_organizer
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set festivalId
     *
     * @param \IFlairFestivalBundle\Entity\festival $festivalId
     *
     * @return festival_organizer
     */
    public function setFestivalId(\IFlairFestivalBundle\Entity\festival $festivalId = null)
    {
        $this->festivalId = $festivalId;

        return $this;
    }

    /**
     * Get festivalId
     *
     * @return \IFlairFestivalBundle\Entity\festival
     */
    public function getFestivalId()
    {
        return $this->festivalId;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return festival_organizer
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
     * Set manager
     *
     * @param string $manager
     *
     * @return festival_organizer
     */
    public function setManager($manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get manager
     *
     * @return string
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set city
     *
     * @param \IFlairSoapBundle\Entity\Partyfindercity $city
     *
     * @return festival_organizer
     */
    public function setCity(\IFlairSoapBundle\Entity\Partyfindercity $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \IFlairSoapBundle\Entity\Partyfindercity
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param \IFlairSoapBundle\Entity\Partyfindercountry $country
     *
     * @return festival_organizer
     */
    public function setCountry(\IFlairSoapBundle\Entity\Partyfindercountry $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \IFlairSoapBundle\Entity\Partyfindercountry
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set host
     *
     * @param string $host
     *
     * @return festival_organizer
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }
}
