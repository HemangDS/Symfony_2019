<?php

namespace Galop\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Advertisement
 *
 * @ORM\Table(name="advertisement")
 * @ORM\Entity(repositoryClass="Galop\AdminBundle\Repository\AdvertisementRepository")
 * @ORM\EntityListeners({"\Galop\AdminBundle\EventListener\AdvertisementEmail"})
 */
class Advertisement
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
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="advertisement")
     * @ORM\JoinColumn(name="userid", referencedColumnName="id")
     */
    protected $userid;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="User", inversedBy="advertisementone")
     * @ORM\JoinColumn(name="updated_by_user", referencedColumnName="id", nullable=true)
     */
    private $updatedByUser;

    /**
     *  @var \DateTime|null
     *
     * @Assert\DateTime()
     * @ORM\Column(name="startdate", type="datetime", nullable=true)
     */
    protected $startdate;

    /**
     *  @var \DateTime|null
     *
     * @Assert\DateTime()
     * @Assert\GreaterThan(propertyPath="startDate")
     * @ORM\Column(name="enddate",type="datetime", nullable=true)
     */
    protected $enddate;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", options={"default": 1})
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="desktop_views", type="string", length=255, nullable=true, options={"default": 0})
     */
    protected $desktop_views;

    /**
     * @var int
     *
     * @ORM\Column(name="desktop_counter", type="bigint", nullable=true, options={"default": 0})
     */
    private $desktop_counter;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile_views", type="string", length=255, nullable=true, options={"default": 0})
     */
    protected $mobile_views;

    /**
     * @var int
     *
     * @ORM\Column(name="mobile_counter", type="bigint", nullable=true, options={"default": 0})
     */
    private $mobile_counter;

    /**
     * @var string
     *
     * @ORM\Column(name="tablet_views", type="string", length=255, nullable=true, options={"default": 0})
     */
    protected $tablet_views;

    /**
     * @var int
     *
     * @ORM\Column(name="tablet_counter", type="bigint", nullable=true, options={"default": 0})
     */
    private $tablet_counter;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="eng_desk_adv", referencedColumnName="id", nullable=false)
     */
    protected $EngDesktopAdd;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="eng_mobile_adv", referencedColumnName="id", nullable=false)
     */
    protected $EngMobileAdd;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="eng_tab_adv", referencedColumnName="id", nullable=false)
     */
    protected $EngTabletAdd;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="dutch_desk_adv", referencedColumnName="id", nullable=false)
     */
    protected $DutchDesktopAdd;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="dutch_mobile_adv", referencedColumnName="id", nullable=false)
     */
    protected $DutchMobileAdd;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="dutch_tab_adv", referencedColumnName="id")
     */
    protected $DutchTabletAdd;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="french_desk_adv", referencedColumnName="id", nullable=false)
     */
    protected $FrenchDesktopAdd;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="french_mobile_adv", referencedColumnName="id", nullable=false)
     */
    protected $FrenchMobileAdd;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="french_tab_adv", referencedColumnName="id", nullable=false)
     */
    protected $FrenchTabletAdd;
    
    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255, nullable=true)
     */
    protected $link;

    /**
     * @var string
     *
     * @ORM\Column(name="emailstatus", type="string", length=255, nullable=true)
     */
    protected $emailstatus;
    
    /**
     * @var \Date|null
     *
     * @ORM\Column(name="weeklyemail", type="date", nullable=true)
     */
    protected $weeklyemail;

    /**
     * @ORM\ManyToOne(targetEntity="Galop\AdminBundle\Entity\AdvertisementZone", inversedBy="advertisement")
     * @ORM\JoinColumn(name="zone_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $zone;

    /**
     * @var string
     *
     * @ORM\Column(name="client_name", type="string", length=255, nullable=true)
     */
    protected $ClientName;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    protected $price;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    protected $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="design_bypweb", type="string", length=255, nullable=true)
     */
    protected $DesignBypweb;

    /**
     * @var \Date|null
     *
     * @Assert\Date()
     * @ORM\Column(name="reminder_date", type="date", nullable=true)
     */
    protected $ReminderDate;

    /**
     * @var string
     *
     * @ORM\Column(name="invoiced", type="string", length=255, nullable=true)
     */
    protected $invoiced;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    protected $address;

    /**
     * @var int
     *
     * @ORM\Column(name="zip", type="integer", length=255, nullable=true)
     */
    protected $ZipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    protected $city;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    protected $country;

    /**
     * @var int
     *
     * @ORM\Column(name="phone_number", type="bigint", length=255, nullable=true)
     */
    protected $phoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    protected $email;

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
     * @return Advertisement
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
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Advertisement
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
     * @return Advertisement
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
     * Set startdate.
     *
     * @param \DateTime|null $startdate
     *
     * @return Advertisement
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
     * @return Advertisement
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
     * Set status.
     *
     * @param bool $status
     *
     * @return News
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Set desktop_views.
     *
     * @param string|null $desktop_views
     *
     * @return Advertisement
     */
    public function setDesktopViews($desktop_views = null)
    {
        $this->desktop_views = $desktop_views;

        return $this;
    }

    /**
     * Get desktop_views.
     *
     * @return string|null
     */
    public function getDesktopViews()
    {
        return $this->desktop_views;
    }

    /**
     * Set mobile_views.
     *
     * @param string|null $mobile_views
     *
     * @return Advertisement
     */
    public function setMobileViews($mobile_views = null)
    {
        $this->mobile_views = $mobile_views;

        return $this;
    }

    /**
     * Get mobile_views.
     *
     * @return string|null
     */
    public function getMobileViews()
    {
        return $this->mobile_views;
    }

    /**
     * Set mobile_counter.
     *
     * @param int|null $mobile_counter
     *
     * @return Advertisement
     */
    public function setMobileCounter($mobile_counter = null)
    {
        $this->mobile_counter = $mobile_counter;

        return $this;
    }

    /**
     * Get mobile_counter.
     *
     * @return int|null
     */
    public function getMobileCounter()
    {
        return $this->mobile_counter;
    }

    /**
     * Set tablet_views.
     *
     * @param string|null $tablet_views
     *
     * @return Advertisement
     */
    public function setTabletViews($tablet_views = null)
    {
        $this->tablet_views = $tablet_views;

        return $this;
    }

    /**
     * Get tablet_views.
     *
     * @return string|null
     */
    public function getTabletViews()
    {
        return $this->tablet_views;
    }

    /**
     * Set tablet_counter.
     *
     * @param int|null $tablet_counter
     *
     * @return Advertisement
     */
    public function setTabletCounter($tablet_counter = null)
    {
        $this->tablet_counter = $tablet_counter;

        return $this;
    }

    /**
     * Get tablet_counter.
     *
     * @return int|null
     */
    public function getTabletCounter()
    {
        return $this->tablet_counter;
    }

    /**
     * Set link.
     *
     * @param string|null $link
     *
     * @return Advertisement
     */
    public function setLink($link = null)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link.
     *
     * @return string|null
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set emailstatus.
     *
     * @param string|null $emailstatus
     *
     * @return Advertisement
     */
    public function setEmailstatus($emailstatus = null)
    {
        $this->emailstatus = $emailstatus;

        return $this;
    }

    /**
     * Get emailstatus.
     *
     * @return string|null
     */
    public function getEmailstatus()
    {
        return $this->emailstatus;
    }

    /**
     * Set weeklyemail.
     *
     * @param \DateTime|null $weeklyemail
     *
     * @return Advertisement
     */
    public function setWeeklyemail($weeklyemail = null)
    {
        $this->weeklyemail = $weeklyemail;

        return $this;
    }

    /**
     * Get weeklyemail.
     *
     * @return \DateTime|null
     */
    public function getWeeklyemail()
    {
        return $this->weeklyemail;
    }

    /**
     * Set userid.
     *
     * @param \Galop\AdminBundle\Entity\User|null $userid
     *
     * @return Advertisement
     */
    public function setUserid(\Galop\AdminBundle\Entity\User $userid = null)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid.
     *
     * @return \Galop\AdminBundle\Entity\User|null
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Set zone.
     *
     * @param \Galop\AdminBundle\Entity\AdvertisementZone|null $zone
     *
     * @return Advertisement
     */
    public function setZone(\Galop\AdminBundle\Entity\AdvertisementZone $zone = null)
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * Get zone.
     *
     * @return \Galop\AdminBundle\Entity\AdvertisementZone|null
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Set desktop_counter.
     *
     * @param int|null $desktop_counter
     *
     * @return Advertisement
     */
    public function setDesktopCounter($desktop_counter = null)
    {
        $this->desktop_counter = $desktop_counter;

        return $this;
    }

    /**
     * Get desktop_counter.
     *
     * @return int|null
     */
    public function getDesktopCounter()
    {
        return $this->desktop_counter;
    }

    /**
     * Set updatedByUser.
     *
     * @param \Galop\AdminBundle\Entity\User|null $updatedByUser
     *
     * @return Advertisement
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

    /**
     * Set engDesktopAdd.
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media|null $engDesktopAdd
     *
     * @return Advertisement
     */
    public function setEngDesktopAdd(\Application\Sonata\MediaBundle\Entity\Media $engDesktopAdd = null)
    {
        $this->EngDesktopAdd = $engDesktopAdd;

        return $this;
    }

    /**
     * Get engDesktopAdd.
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media|null
     */
    public function getEngDesktopAdd()
    {
        return $this->EngDesktopAdd;
    }

    /**
     * Set engMobileAdd.
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media|null $engMobileAdd
     *
     * @return Advertisement
     */
    public function setEngMobileAdd(\Application\Sonata\MediaBundle\Entity\Media $engMobileAdd = null)
    {
        $this->EngMobileAdd = $engMobileAdd;

        return $this;
    }

    /**
     * Get engMobileAdd.
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media|null
     */
    public function getEngMobileAdd()
    {
        return $this->EngMobileAdd;
    }

    /**
     * Set engTabletAdd.
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media|null $engTabletAdd
     *
     * @return Advertisement
     */
    public function setEngTabletAdd(\Application\Sonata\MediaBundle\Entity\Media $engTabletAdd = null)
    {
        $this->EngTabletAdd = $engTabletAdd;

        return $this;
    }

    /**
     * Get engTabletAdd.
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media|null
     */
    public function getEngTabletAdd()
    {
        return $this->EngTabletAdd;
    }

    /**
     * Set dutchDesktopAdd.
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media|null $dutchDesktopAdd
     *
     * @return Advertisement
     */
    public function setDutchDesktopAdd(\Application\Sonata\MediaBundle\Entity\Media $dutchDesktopAdd = null)
    {
        $this->DutchDesktopAdd = $dutchDesktopAdd;

        return $this;
    }

    /**
     * Get dutchDesktopAdd.
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media|null
     */
    public function getDutchDesktopAdd()
    {
        return $this->DutchDesktopAdd;
    }

    /**
     * Set dutchMobileAdd.
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media|null $dutchMobileAdd
     *
     * @return Advertisement
     */
    public function setDutchMobileAdd(\Application\Sonata\MediaBundle\Entity\Media $dutchMobileAdd = null)
    {
        $this->DutchMobileAdd = $dutchMobileAdd;

        return $this;
    }

    /**
     * Get dutchMobileAdd.
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media|null
     */
    public function getDutchMobileAdd()
    {
        return $this->DutchMobileAdd;
    }

    /**
     * Set dutchTabletAdd.
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media|null $dutchTabletAdd
     *
     * @return Advertisement
     */
    public function setDutchTabletAdd(\Application\Sonata\MediaBundle\Entity\Media $dutchTabletAdd = null)
    {
        $this->DutchTabletAdd = $dutchTabletAdd;

        return $this;
    }

    /**
     * Get dutchTabletAdd.
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media|null
     */
    public function getDutchTabletAdd()
    {
        return $this->DutchTabletAdd;
    }

    /**
     * Set frenchDesktopAdd.
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media|null $frenchDesktopAdd
     *
     * @return Advertisement
     */
    public function setFrenchDesktopAdd(\Application\Sonata\MediaBundle\Entity\Media $frenchDesktopAdd = null)
    {
        $this->FrenchDesktopAdd = $frenchDesktopAdd;

        return $this;
    }

    /**
     * Get frenchDesktopAdd.
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media|null
     */
    public function getFrenchDesktopAdd()
    {
        return $this->FrenchDesktopAdd;
    }

    /**
     * Set frenchMobileAdd.
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media|null $frenchMobileAdd
     *
     * @return Advertisement
     */
    public function setFrenchMobileAdd(\Application\Sonata\MediaBundle\Entity\Media $frenchMobileAdd = null)
    {
        $this->FrenchMobileAdd = $frenchMobileAdd;

        return $this;
    }

    /**
     * Get frenchMobileAdd.
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media|null
     */
    public function getFrenchMobileAdd()
    {
        return $this->FrenchMobileAdd;
    }

    /**
     * Set frenchTabletAdd.
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media|null $frenchTabletAdd
     *
     * @return Advertisement
     */
    public function setFrenchTabletAdd(\Application\Sonata\MediaBundle\Entity\Media $frenchTabletAdd = null)
    {
        $this->FrenchTabletAdd = $frenchTabletAdd;

        return $this;
    }

    /**
     * Get frenchTabletAdd.
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media|null
     */
    public function getFrenchTabletAdd()
    {
        return $this->FrenchTabletAdd;
    }

    /**
     * Set clientName.
     *
     * @param string|null $clientName
     *
     * @return Advertisement
     */
    public function setClientName($clientName = null)
    {
        $this->ClientName = $clientName;

        return $this;
    }

    /**
     * Get clientName.
     *
     * @return string|null
     */
    public function getClientName()
    {
        return $this->ClientName;
    }

    /**
     * Set price.
     *
     * @param float|null $price
     *
     * @return Advertisement
     */
    public function setPrice($price = null)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return float|null
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set remarks.
     *
     * @param string|null $remarks
     *
     * @return Advertisement
     */
    public function setRemarks($remarks = null)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * Get remarks.
     *
     * @return string|null
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * Set designBypweb.
     *
     * @param string|null $designBypweb
     *
     * @return Advertisement
     */
    public function setDesignBypweb($designBypweb = null)
    {
        $this->DesignBypweb = $designBypweb;

        return $this;
    }

    /**
     * Get designBypweb.
     *
     * @return string|null
     */
    public function getDesignBypweb()
    {
        return $this->DesignBypweb;
    }

    /**
     * Set reminderDate.
     *
     * @param \DateTime|null $reminderDate
     *
     * @return Advertisement
     */
    public function setReminderDate($reminderDate = null)
    {
        $this->ReminderDate = $reminderDate;

        return $this;
    }

    /**
     * Get reminderDate.
     *
     * @return \DateTime|null
     */
    public function getReminderDate()
    {
        return $this->ReminderDate;
    }

    /**
     * Set invoiced.
     *
     * @param string|null $invoiced
     *
     * @return Advertisement
     */
    public function setInvoiced($invoiced = null)
    {
        $this->invoiced = $invoiced;

        return $this;
    }

    /**
     * Get invoiced.
     *
     * @return string|null
     */
    public function getInvoiced()
    {
        return $this->invoiced;
    }

    /**
     * Set name.
     *
     * @param string|null $name
     *
     * @return Advertisement
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address.
     *
     * @param string|null $address
     *
     * @return Advertisement
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
     * Set zipCode.
     *
     * @param int|null $zipCode
     *
     * @return Advertisement
     */
    public function setZipCode($zipCode = null)
    {
        $this->ZipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode.
     *
     * @return int|null
     */
    public function getZipCode()
    {
        return $this->ZipCode;
    }

    /**
     * Set city.
     *
     * @param string|null $city
     *
     * @return Advertisement
     */
    public function setCity($city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return string|null
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country.
     *
     * @param string|null $country
     *
     * @return Advertisement
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
     * Set phoneNumber.
     *
     * @param int|null $phoneNumber
     *
     * @return Advertisement
     */
    public function setPhoneNumber($phoneNumber = null)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber.
     *
     * @return int|null
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set email.
     *
     * @param string|null $email
     *
     * @return Advertisement
     */
    public function setEmail($email = null)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }
}
