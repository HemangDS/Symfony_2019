<?php

namespace iFlair\LetsBonusMigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Companies.
 *
 * @ORM\Table(name="companies")
 * @ORM\Entity
 */
class Companies
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
     * @ORM\Column(name="currency", type="string", length=20, nullable=true)
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="iso_code", type="string", length=20, nullable=true)
     */
    private $isoCode;

    /**
     * @var string
     *
     * @ORM\Column(name="lang", type="string", length=20, nullable=true)
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="common_conditions", type="text", length=65535, nullable=true)
     */
    private $commonConditions;

    /**
     * @var int
     *
     * @ORM\Column(name="hours_offset", type="integer", nullable=true)
     */
    private $hoursOffset = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string", length=255, nullable=true)
     */
    private $timezone;

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
     * @return Companies
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
     * Set currency.
     *
     * @param string $currency
     *
     * @return Companies
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set isoCode.
     *
     * @param string $isoCode
     *
     * @return Companies
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * Get isoCode.
     *
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * Set lang.
     *
     * @param string $lang
     *
     * @return Companies
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang.
     *
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set commonConditions.
     *
     * @param string $commonConditions
     *
     * @return Companies
     */
    public function setCommonConditions($commonConditions)
    {
        $this->commonConditions = $commonConditions;

        return $this;
    }

    /**
     * Get commonConditions.
     *
     * @return string
     */
    public function getCommonConditions()
    {
        return $this->commonConditions;
    }

    /**
     * Set hoursOffset.
     *
     * @param int $hoursOffset
     *
     * @return Companies
     */
    public function setHoursOffset($hoursOffset)
    {
        $this->hoursOffset = $hoursOffset;

        return $this;
    }

    /**
     * Get hoursOffset.
     *
     * @return int
     */
    public function getHoursOffset()
    {
        return $this->hoursOffset;
    }

    /**
     * Set timezone.
     *
     * @param string $timezone
     *
     * @return Companies
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone.
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Companies
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
     * @return Companies
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
