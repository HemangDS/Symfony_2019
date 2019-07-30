<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FestivalInprogressDates
 *
 * @ORM\Table(name="festival_inprogress_dates")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\FestivalInprogressDatesRepository")
 */
class FestivalInprogressDates
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
     * @var \DateTime
     *
     * @ORM\Column(name="start_dates", type="date")
     */
    private $start_dates;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_dates", type="date")
     */
    private $end_dates;


    /**
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\FestivalInprogress", inversedBy="festival_inprogress_dates")
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
     * Set festivalInprogressId
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogress $festivalInprogressId
     *
     * @return FestivalInprogressDates
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

    /**
     * Set startDates
     *
     * @param \DateTime $startDates
     *
     * @return FestivalInprogressDates
     */
    public function setStartDates($startDates)
    {
        $this->start_dates = $startDates;

        return $this;
    }

    /**
     * Get startDates
     *
     * @return \DateTime
     */
    public function getStartDates()
    {
        return $this->start_dates;
    }

    /**
     * Set endDates
     *
     * @param \DateTime $endDates
     *
     * @return FestivalInprogressDates
     */
    public function setEndDates($endDates)
    {
        $this->end_dates = $endDates;

        return $this;
    }

    /**
     * Get endDates
     *
     * @return \DateTime
     */
    public function getEndDates()
    {
        return $this->end_dates;
    }
}
