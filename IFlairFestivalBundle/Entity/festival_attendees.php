<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * festival_attendees
 *
 * @ORM\Table(name="festival_attendees")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\festival_attendeesRepository")
 */
class festival_attendees
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
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\festival", inversedBy="festival_attendees")
     * @ORM\JoinColumn(name = "festival_id", referencedColumnName = "id")
     */
    private $festivalId;

    /**
     * @var string
     *
     * @ORM\Column(name="attendees", type="string", length=255)
     */
    private $attendees;

    /**
     * @var string
     *
     * @ORM\Column(name="attendees_year", type="string", length=255)
     */
    private $attendeesYear;


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
     * Set attendees
     *
     * @param string $attendees
     *
     * @return festival_attendees
     */
    public function setAttendees($attendees)
    {
        $this->attendees = $attendees;

        return $this;
    }

    /**
     * Get attendees
     *
     * @return string
     */
    public function getAttendees()
    {
        return $this->attendees;
    }

    /**
     * Set attendeesYear
     *
     * @param string $attendeesYear
     *
     * @return festival_attendees
     */
    public function setAttendeesYear($attendeesYear)
    {
        $this->attendeesYear = $attendeesYear;

        return $this;
    }

    /**
     * Get attendeesYear
     *
     * @return string
     */
    public function getAttendeesYear()
    {
        return $this->attendeesYear;
    }

    /**
     * Set festivalId
     *
     * @param \IFlairFestivalBundle\Entity\festival $festivalId
     *
     * @return festival_attendees
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
}
