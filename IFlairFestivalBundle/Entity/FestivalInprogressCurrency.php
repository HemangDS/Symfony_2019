<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FestivalInprogressCurrency
 *
 * @ORM\Table(name="festival_inprogress_currency")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\FestivalInprogressCurrencyRepository")
 */
class FestivalInprogressCurrency
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
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\currency", inversedBy="festival_currency")
     * @ORM\JoinColumn(name = "currency_id", referencedColumnName = "id")
     */
    private $currencyId;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\FestivalInprogress", inversedBy="festival_inprogress_currency")
     * @ORM\JoinColumn(name = "festival_inprogress_id", referencedColumnName = "id")
     */
    private $festivalInprogressId;

    public function __toString()
    {
        return strval($this->id);
    }
    
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
     * Set currencyId
     *
     * @param \IFlairFestivalBundle\Entity\currency $currencyId
     *
     * @return FestivalInprogressCurrency
     */
    public function setCurrencyId(\IFlairFestivalBundle\Entity\currency $currencyId = null)
    {
        $this->currencyId = $currencyId;

        return $this;
    }

    /**
     * Get currencyId
     *
     * @return \IFlairFestivalBundle\Entity\currency
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    /**
     * Set festivalInprogressId
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogress $festivalInprogressId
     *
     * @return FestivalInprogressCurrency
     */
    public function setFestivalInprogressId(\IFlairFestivalBundle\Entity\FestivalInprogress $festivalInprogressId = null)
    {
        $this->festivalInprogressId = $festivalInprogressId;

        return $this;
    }

    /**
     * Get festivalInprogressId
     *
     * @return \IFlairFestivalBundle\Entity\FestivalInprogress
     */
    public function getFestivalInprogressId()
    {
        return $this->festivalInprogressId;
    }
}
