<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContributionAddFestival
 *
 * @ORM\Table(name="contribution_add_festival")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\ContributionAddFestivalRepository")
 */
class ContributionAddFestival
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="contribution_add_festival")
     * @ORM\JoinColumn(name = "user_id", referencedColumnName = "id")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="festival_name", type="string", length=255)
     */
    private $festivalName;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairSoapBundle\Entity\Partyfindercountry", inversedBy="contribution_add_festival")
     * @ORM\JoinColumn(name = "country_id", referencedColumnName = "id")
     */
    private $countryId;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairSoapBundle\Entity\Partyfindercity", inversedBy="contribution_add_festival")
     * @ORM\JoinColumn(name = "city_id", referencedColumnName = "id")
     */
    private $cityId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;


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
     * Set festivalName
     *
     * @param string $festivalName
     *
     * @return ContributionAddFestival
     */
    public function setFestivalName($festivalName)
    {
        $this->festivalName = $festivalName;

        return $this;
    }

    /**
     * Get festivalName
     *
     * @return string
     */
    public function getFestivalName()
    {
        return $this->festivalName;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return ContributionAddFestival
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set userId
     *
     * @param \AppBundle\Entity\User $userId
     *
     * @return ContributionAddFestival
     */
    public function setUserId(\AppBundle\Entity\User $userId = null)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return \AppBundle\Entity\User
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set countryId
     *
     * @param \IFlairSoapBundle\Entity\Partyfindercountry $countryId
     *
     * @return ContributionAddFestival
     */
    public function setCountryId(\IFlairSoapBundle\Entity\Partyfindercountry $countryId = null)
    {
        $this->countryId = $countryId;

        return $this;
    }

    /**
     * Get countryId
     *
     * @return \IFlairSoapBundle\Entity\Partyfindercountry
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * Set cityId
     *
     * @param \IFlairSoapBundle\Entity\Partyfindercity $cityId
     *
     * @return ContributionAddFestival
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
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return ContributionAddFestival
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
}
