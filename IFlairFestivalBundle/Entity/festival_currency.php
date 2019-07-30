<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * festival_currency
 *
 * @ORM\Table(name="festival_currency")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\festival_currencyRepository")
 */
class festival_currency
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
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\festival", inversedBy="festival_currency")
     * @ORM\JoinColumn(name = "festival_id", referencedColumnName = "id")
     */
    private $festivalId;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\currency", inversedBy="festival_currency")
     * @ORM\JoinColumn(name = "currency_id", referencedColumnName = "id")
     */
    private $currencyId;


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
     * Set festivalId
     *
     * @param \IFlairFestivalBundle\Entity\festival $festivalId
     *
     * @return festival_currency
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
     * Set currencyId
     *
     * @param \IFlairFestivalBundle\Entity\currency $currencyId
     *
     * @return festival_currency
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
}
