<?php

//  We have remvoed is_approved field from here and udpated with status_id : Need to change all use of this festival inprogress

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
/**
 * FestivalInprogress
 *
 * @ORM\Table(name="festival_inprogress")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\FestivalInprogressRepository")
 * @Vich\Uploadable
 */
class FestivalInprogress
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairSoapBundle\Entity\Partyfindercountry")
     * @ORM\JoinColumn(name = "country_id", referencedColumnName = "id", nullable=true)
     */
    private $countryId;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairSoapBundle\Entity\Partyfindercity")
     * @ORM\JoinColumn(name = "city_id", referencedColumnName = "id", nullable=true)
     */
    private $cityId;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @var string
     *
     * @ORM\Column(name="header", type="string", length=255, nullable=true)
     */
    private $header;

    /**
     * @var string
     *
     * @ORM\Column(name="attendies", type="string", length=255, nullable=true)
     */
    private $attendies;

    /**
     * @var string
     *
     * @ORM\Column(name="stages", type="string", length=255, nullable=true)
     */
    private $stages;

    /**
     * @var string
     *
     * @ORM\Column(name="held_since", type="string", length=255, nullable=true)
     */
    private $heldSince;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="host", type="string", length=255, nullable=true)
     */
    private $host;

    /**
     * @var string
     *
     * @ORM\Column(name="manager", type="string", length=255, nullable=true)
     */
    private $manager;

    /**
     * @var string
     *
     * @ORM\Column(name="host_website", type="string", length=255, nullable=true)
     */
    private $hostWebsite;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\festival", inversedBy="festival_artist")
     * @ORM\JoinColumn(name = "festival_id", referencedColumnName = "id", nullable=true)
     */
    private $festivalId;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="settings")
     * @ORM\JoinColumn(name = "user_id", referencedColumnName = "id", nullable=true)
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairSoapBundle\Entity\ContributionStatus", inversedBy="festival_inprogress_status")
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     */
    private $createdDate;

    /**
    * @var FestivalInprogressCurrency[]
    * @ORM\OneToMany(targetEntity="IFlairFestivalBundle\Entity\FestivalInprogressCurrency", mappedBy="festivalInprogressId", cascade={"persist", "remove"})
    */
    private $currency;

    /**
    * @var FestivalInprogressDates[]
    * @ORM\OneToMany(targetEntity="IFlairFestivalBundle\Entity\FestivalInprogressDates", mappedBy="festivalInprogressId", cascade={"persist", "remove"})
    */
    private $dates;

    /**
    * @var FestivalInprogressFeatures[]
    * @ORM\OneToMany(targetEntity="IFlairFestivalBundle\Entity\FestivalInprogressFeatures", mappedBy="festivalInprogressId", cascade={"persist", "remove"})
    */
    private $features;

    /**
    * @var FestivalInprogressMusicgenre[]
    * @ORM\OneToMany(targetEntity="IFlairFestivalBundle\Entity\FestivalInprogressMusicgenre", mappedBy="festivalInprogressId", cascade={"persist", "remove"})
    */
    private $festival_inprogress_musicgenre;

    /**
    * @var FestivalInprogressPayments[]
    * @ORM\OneToMany(targetEntity="IFlairFestivalBundle\Entity\FestivalInprogressPayments", mappedBy="festivalInprogressId", cascade={"persist", "remove"})
    */
    private $festival_inprogress_payments;

    /**
    * @var FestivalInprogressRatings[]
    * @ORM\OneToMany(targetEntity="IFlairFestivalBundle\Entity\FestivalInprogressRatings", mappedBy="festivalInprogressId", cascade={"persist", "remove"})
    */
    private $festival_inprogress_rating;

    /**
    * @var Festival_inprogress_status[]
    * @ORM\OneToMany(targetEntity="IFlairFestivalBundle\Entity\Festival_inprogress_status", mappedBy="festivalInprogressId", cascade={"persist", "remove"})
    */
    private $Festival_inprogress_status;

    /**
     * @Vich\UploadableField(mapping="contribution_fest_images", fileNameProperty="logo")
     * @var File
     */
    private $imageFile;

    /**
     * @var string
     *
     * @ORM\Column(name="updated_fields", type="text", nullable=true, length=2000)
     */
    private $updatedFields;

    /**
     * @ORM\OneToMany(targetEntity="IFlairFestivalBundle\Entity\FestivalInprogressCurrency", mappedBy="festivalInprogressId", cascade={"remove"})
     */
    private $festival_inprogress_currency;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->currency = new \Doctrine\Common\Collections\ArrayCollection();
        $this->dates = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdDate = new \DateTime(); 
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
     * Set name
     *
     * @param string $name
     *
     * @return FestivalInprogress
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set logo
     *
     * @param string $logo
     *
     * @return FestivalInprogress
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        if(!empty($this->logo)){
            $logo = explode("web/",$this->logo);
        }
        if(!empty($logo[1])){
            return $logo[1];
        }
        else{
            return $this->logo;
        }
    }

    /**
     * Set header
     *
     * @param string $header
     *
     * @return FestivalInprogress
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Get header
     *
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set attendies
     *
     * @param string $attendies
     *
     * @return FestivalInprogress
     */
    public function setAttendies($attendies)
    {
        $this->attendies = $attendies;

        return $this;
    }

    /**
     * Get attendies
     *
     * @return string
     */
    public function getAttendies()
    {
        return $this->attendies;
    }

    /**
     * Set stages
     *
     * @param string $stages
     *
     * @return FestivalInprogress
     */
    public function setStages($stages)
    {
        $this->stages = $stages;

        return $this;
    }

    /**
     * Get stages
     *
     * @return string
     */
    public function getStages()
    {
        return $this->stages;
    }

    /**
     * Set heldSince
     *
     * @param string $heldSince
     *
     * @return FestivalInprogress
     */
    public function setHeldSince($heldSince)
    {
        $this->heldSince = $heldSince;

        return $this;
    }

    /**
     * Get heldSince
     *
     * @return string
     */
    public function getHeldSince()
    {
        return $this->heldSince;
    }

    /**
     * Set website
     *
     * @param string $website
     *
     * @return FestivalInprogress
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
     * Set email
     *
     * @param string $email
     *
     * @return FestivalInprogress
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
     * Set host
     *
     * @param string $host
     *
     * @return FestivalInprogress
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

    /**
     * Set manager
     *
     * @param string $manager
     *
     * @return FestivalInprogress
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
     * Set hostWebsite
     *
     * @param string $hostWebsite
     *
     * @return FestivalInprogress
     */
    public function setHostWebsite($hostWebsite)
    {
        $this->hostWebsite = $hostWebsite;

        return $this;
    }

    /**
     * Get hostWebsite
     *
     * @return string
     */
    public function getHostWebsite()
    {
        return $this->hostWebsite;
    }

    /**
     * Set festivalId
     *
     * @param \IFlairFestivalBundle\Entity\festival $festivalId
     *
     * @return FestivalInprogress
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
     * Set userId
     *
     * @param \AppBundle\Entity\User $userId
     *
     * @return FestivalInprogress
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
     * @return FestivalInprogress
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
     * @return FestivalInprogress
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
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return FestivalInprogress
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }
    

    /**
     * Add currency
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogressCurrency $currency
     *
     * @return FestivalInprogress
     */
    public function addCurrency(\IFlairFestivalBundle\Entity\FestivalInprogressCurrency $currency)
    {
        $this->currency[] = $currency;

        return $this;
    }

    /**
     * Remove currency
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogressCurrency $currency
     */
    public function removeCurrency(\IFlairFestivalBundle\Entity\FestivalInprogressCurrency $currency)
    {
        $this->currency->removeElement($currency);
    }

    /**
     * Get currency
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Add date
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogressDates $date
     *
     * @return FestivalInprogress
     */
    public function addDate(\IFlairFestivalBundle\Entity\FestivalInprogressDates $date)
    {
        $this->dates[] = $date;

        return $this;
    }

    /**
     * Remove date
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogressDates $date
     */
    public function removeDate(\IFlairFestivalBundle\Entity\FestivalInprogressDates $date)
    {
        $this->dates->removeElement($date);
    }

    /**
     * Get dates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDates()
    {
        return $this->dates;
    }

    /**
     * Add festivalInprogressDate
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogressDates $festivalInprogressDate
     *
     * @return FestivalInprogress
     */
    public function addFestivalInprogressDate(\IFlairFestivalBundle\Entity\FestivalInprogressDates $festivalInprogressDate)
    {
        $this->FestivalInprogressDate[] = $festivalInprogressDate;

        return $this;
    }

    /**
     * Remove festivalInprogressDate
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogressDates $festivalInprogressDate
     */
    public function removeFestivalInprogressDate(\IFlairFestivalBundle\Entity\FestivalInprogressDates $festivalInprogressDate)
    {
        $this->FestivalInprogressDate->removeElement($festivalInprogressDate);
    }

    /**
     * Get festivalInprogressDate
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFestivalInprogressDate()
    {
        return $this->FestivalInprogressDate;
    }

    /**
     * Add festivalInprogressCurrency
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogressCurrency $festivalInprogressCurrency
     *
     * @return FestivalInprogress
     */
    public function addFestivalInprogressCurrency(\IFlairFestivalBundle\Entity\FestivalInprogressCurrency $festivalInprogressCurrency)
    {
        $this->FestivalInprogressCurrency[] = $festivalInprogressCurrency;

        return $this;
    }

    /**
     * Remove festivalInprogressCurrency
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogressCurrency $festivalInprogressCurrency
     */
    public function removeFestivalInprogressCurrency(\IFlairFestivalBundle\Entity\FestivalInprogressCurrency $festivalInprogressCurrency)
    {
        $this->FestivalInprogressCurrency->removeElement($festivalInprogressCurrency);
    }

    /**
     * Get festivalInprogressCurrency
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFestivalInprogressCurrency()
    {
        return $this->FestivalInprogressCurrency;
    }

    /**
     * Get festivalInprogressDates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFestivalInprogressDates()
    {
        return $this->FestivalInprogressDates;
    }

    /**
     * Add feature
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogressFeatures $feature
     *
     * @return FestivalInprogress
     */
    public function addFeature(\IFlairFestivalBundle\Entity\FestivalInprogressFeatures $feature)
    {
        $this->features[] = $feature;

        return $this;
    }

    /**
     * Remove feature
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogressFeatures $feature
     */
    public function removeFeature(\IFlairFestivalBundle\Entity\FestivalInprogressFeatures $feature)
    {
        $this->features->removeElement($feature);
    }

    /**
     * Get features
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * Add festivalInprogressMusicgenre
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogressMusicgenre $festivalInprogressMusicgenre
     *
     * @return FestivalInprogress
     */
    public function addFestivalInprogressMusicgenre(\IFlairFestivalBundle\Entity\FestivalInprogressMusicgenre $festivalInprogressMusicgenre)
    {
        $this->festival_inprogress_musicgenre[] = $festivalInprogressMusicgenre;

        return $this;
    }

    /**
     * Remove festivalInprogressMusicgenre
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogressMusicgenre $festivalInprogressMusicgenre
     */
    public function removeFestivalInprogressMusicgenre(\IFlairFestivalBundle\Entity\FestivalInprogressMusicgenre $festivalInprogressMusicgenre)
    {
        $this->festival_inprogress_musicgenre->removeElement($festivalInprogressMusicgenre);
    }

    /**
     * Get festivalInprogressMusicgenre
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFestivalInprogressMusicgenre()
    {
        return $this->festival_inprogress_musicgenre;
    }

    /**
     * Add festivalInprogressPayment
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogressPayments $festivalInprogressPayment
     *
     * @return FestivalInprogress
     */
    public function addFestivalInprogressPayment(\IFlairFestivalBundle\Entity\FestivalInprogressPayments $festivalInprogressPayment)
    {
        $this->festival_inprogress_payments[] = $festivalInprogressPayment;

        return $this;
    }

    /**
     * Remove festivalInprogressPayment
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogressPayments $festivalInprogressPayment
     */
    public function removeFestivalInprogressPayment(\IFlairFestivalBundle\Entity\FestivalInprogressPayments $festivalInprogressPayment)
    {
        $this->festival_inprogress_payments->removeElement($festivalInprogressPayment);
    }

    /**
     * Get festivalInprogressPayments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFestivalInprogressPayments()
    {
        return $this->festival_inprogress_payments;
    }

    /**
     * Add festivalInprogressRating
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogressRatings $festivalInprogressRating
     *
     * @return FestivalInprogress
     */
    public function addFestivalInprogressRating(\IFlairFestivalBundle\Entity\FestivalInprogressRatings $festivalInprogressRating)
    {
        $this->festival_inprogress_rating[] = $festivalInprogressRating;

        return $this;
    }

    /**
     * Remove festivalInprogressRating
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogressRatings $festivalInprogressRating
     */
    public function removeFestivalInprogressRating(\IFlairFestivalBundle\Entity\FestivalInprogressRatings $festivalInprogressRating)
    {
        $this->festival_inprogress_rating->removeElement($festivalInprogressRating);
    }

    /**
     * Get festivalInprogressRating
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFestivalInprogressRating()
    {
        return $this->festival_inprogress_rating;
    }

    /**
     * Add festivalInprogressStatus
     *
     * @param \IFlairFestivalBundle\Entity\Festival_inprogress_status $festivalInprogressStatus
     *
     * @return FestivalInprogress
     */
    public function addFestivalInprogressStatus(\IFlairFestivalBundle\Entity\Festival_inprogress_status $festivalInprogressStatus)
    {
        $this->Festival_inprogress_status[] = $festivalInprogressStatus;

        return $this;
    }

    /**
     * Extra added for admin panel create festival inprogress
     */
    public function setFestivalInprogressStatus($festivalInprogressStatus)
    {
        $this->Festival_inprogress_status = $festivalInprogressStatus;
        return $this;
    }

    /**
     * Remove festivalInprogressStatus
     *
     * @param \IFlairFestivalBundle\Entity\Festival_inprogress_status $festivalInprogressStatus
     */
    public function removeFestivalInprogressStatus(\IFlairFestivalBundle\Entity\Festival_inprogress_status $festivalInprogressStatus)
    {
        $this->Festival_inprogress_status->removeElement($festivalInprogressStatus);
    }

    /**
     * Get festivalInprogressStatus
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFestivalInprogressStatus()
    {
        return $this->Festival_inprogress_status;
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->createdDate = new \DateTime('now');
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }


    /**
     * Set updatedFields.
     *
     * @param string|null $updatedFields
     *
     * @return FestivalInprogress
     */
    public function setUpdatedFields($updatedFields = null)
    {
        $this->updatedFields = $updatedFields;

        return $this;
    }

    /**
     * Get updatedFields.
     *
     * @return string|null
     */
    public function getUpdatedFields()
    {
        return $this->updatedFields;
    }

    /**
     * Set status
     *
     * @param \IFlairSoapBundle\Entity\ContributionStatus $status
     *
     * @return FestivalInprogress
     */
    public function setStatus(\IFlairSoapBundle\Entity\ContributionStatus $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \IFlairSoapBundle\Entity\ContributionStatus
     */
    public function getStatus()
    {
        return $this->status;
    }
}
