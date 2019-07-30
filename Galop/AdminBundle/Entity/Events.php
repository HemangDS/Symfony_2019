<?php

namespace Galop\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Events
 *
 * @ORM\Table(name="events")
 * @ORM\Entity(repositoryClass="Galop\AdminBundle\Repository\EventsRepository")
 */
class Events
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
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    protected $address;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    protected $country;

    /**
     * @var \DateTime|null
     *
     * @Assert\DateTime()
     * @ORM\Column(name="startdate", type="datetime", nullable=true)
     */
    protected $startdate;

    /**
     * @var \DateTime|null
     *
     * @Assert\DateTime()
     * @Assert\GreaterThan(propertyPath="startDate")
     * @ORM\Column(name="enddate",type="datetime", nullable=true)
     */
    protected $enddate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    protected $website;

    /**
     * @var string|null
     *
     * @ORM\Column(name="schedule", type="string", length=255, nullable=true)
     */
    protected $schedule;

    /**
     * @var string|null
     *
     * @ORM\Column(name="timetable", type="string", length=255, nullable=true)
     */
    protected $timetable;

    /**
     * @var string|null
     *
     * @ORM\Column(name="results", type="string", length=255, nullable=true)
     */
    protected $results;

    /**
     * @var string|null
     *
     * @ORM\Column(name="livestream", type="string", length=255, nullable=true)
     */
    protected $livestream;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="User", inversedBy="eventscreate")
     * @ORM\JoinColumn(name="created_by_user", referencedColumnName="id", nullable=true)
     */
    protected $createdByUser;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="User", inversedBy="eventsupdate")
     * @ORM\JoinColumn(name="updated_by_user", referencedColumnName="id", nullable=true)
     */
    protected $updatedByUser;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="eventstatus", type="string", length=255, nullable=true)
     */
    protected $eventstatus;
    
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
     * @return Events
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
     * Set address.
     *
     * @param string|null $address
     *
     * @return Events
     */
    public function setAddress($address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set country.
     *
     * @param string|null $country
     *
     * @return Events
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
     * Set startdate.
     *
     * @param \DateTime|null $startdate
     *
     * @return Events
     */
    public function setStartdate($startdate = null)
    {
        $this->startdate = $startdate;

        return $this;
    }

    /**
     * Get startdate.
     *
     * @return \DateTime|null
     */
    public function getStartdate()
    {
        return $this->startdate;
    }

    /**
     * Set enddate.
     *
     * @param \DateTime|null $enddate
     *
     * @return Events
     */
    public function setEnddate($enddate = null)
    {
        $this->enddate = $enddate;

        return $this;
    }

    /**
     * Get enddate.
     *
     * @return \DateTime|null
     */
    public function getEnddate()
    {
        return $this->enddate;
    }

    /**
     * Set website.
     *
     * @param string|null $website
     *
     * @return Events
     */
    public function setWebsite($website = null)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website.
     *
     * @return string|null
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set schedule.
     *
     * @param string|null $schedule
     *
     * @return Events
     */
    public function setSchedule($schedule = null)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule.
     *
     * @return string|null
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Set timetable.
     *
     * @param string|null $timetable
     *
     * @return Events
     */
    public function setTimetable($timetable = null)
    {
        $this->timetable = $timetable;

        return $this;
    }

    /**
     * Get timetable.
     *
     * @return string|null
     */
    public function getTimetable()
    {
        return $this->timetable;
    }

    /**
     * Set results.
     *
     * @param string|null $results
     *
     * @return Events
     */
    public function setResults($results = null)
    {
        $this->results = $results;

        return $this;
    }

    /**
     * Get results.
     *
     * @return string|null
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Set livestream.
     *
     * @param string|null $livestream
     *
     * @return Events
     */
    public function setLivestream($livestream = null)
    {
        $this->livestream = $livestream;

        return $this;
    }

    /**
     * Get livestream.
     *
     * @return string|null
     */
    public function getLivestream()
    {
        return $this->livestream;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Events
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
     * @return Events
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
     * Set eventstatus.
     *
     * @param string $eventstatus
     *
     * @return Events
     */
    public function setEventstatus($eventstatus)
    {
        $this->eventstatus = $eventstatus;

        return $this;
    }

    /**
     * Get eventstatus.
     *
     * @return string
     */
    public function getEventstatus()
    {
        return $this->eventstatus;
    }

    /**
     * Set createdByUser.
     *
     * @param \Galop\AdminBundle\Entity\User|null $createdByUser
     *
     * @return Events
     */
    public function setCreatedByUser(\Galop\AdminBundle\Entity\User $createdByUser = null)
    {
        $this->createdByUser = $createdByUser;

        return $this;
    }

    /**
     * Get createdByUser.
     *
     * @return \Galop\AdminBundle\Entity\User|null
     */
    public function getCreatedByUser()
    {
        return $this->createdByUser;
    }

    /**
     * Set updatedByUser.
     *
     * @param \Galop\AdminBundle\Entity\User|null $updatedByUser
     *
     * @return Events
     */
    public function setUpdatedByUser(\Galop\AdminBundle\Entity\User $updatedByUser = null)
    {
        $this->updatedByUser = $updatedByUser;

        return $this;
    }

    /**
     * Get updatedByUser.
     *
     * @return \Galop\AdminBundle\Entity\User|null
     */
    public function getUpdatedByUser()
    {
        return $this->updatedByUser;
    }
}
