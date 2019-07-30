<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#use Doctrine\Common\Collections\ArrayCollection;

/**
 * Voucher.
 *
 * @ORM\Table(name="lb_voucher")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\VoucherRepository")
 */
class Voucher
{
    const COUPON = 1;
    const DISCOUNT = 2;
    const FREEARTICLES = 3;
    const FREESHIPPING = 4;
    const DRAW = 5;
    const NO = 0;
    const YES = 1;
    const VOUCHERDEACTIVE = 0;
    const VOUCHERACTIVE = 1;
    const VOUCHEREXPIRED = 2;
    const VOUCHERDEFAULTLANGCODE = 'EN';
    const VOUCHERDEFAULTCURRENCYCODE = 'EUR';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="ref_voucher_id", type="integer")
     */
    private $refVoucherId;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publish_start_date", type="datetime")
     */
    private $publishStartDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publish_end_date", type="datetime")
     */
    private $publishEndDate;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="short_description", type="string", length=255, nullable=true)
     */
    private $shortDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="voucher_type_id", type="integer")
     */
    private $voucherTypeId;

    /**
     * @var string
     *
     * @ORM\Column(name="default_track_uri", type="text")
     */
    private $defaultTrackUri;

    /**
     * @var int
     *
     * @ORM\Column(name="site_specific", type="integer")
     */
    private $siteSpecific;

    /**
     * @var string
     *
     * @ORM\Column(name="landing_url", type="text", nullable=true)
     */
    private $landingUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="discount_amount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $discountAmount;

    /**
     * @var int
     *
     * @ORM\Column(name="is_percentage", type="integer")
     */
    private $isPercentage;

    /**
     * @var string
     *
     * @ORM\Column(name="publisher_info", type="text", nullable=true)
     */
    private $publisherInfo;

    /**
     * @var int
     *
     * @ORM\Column(name="exclusive", type="integer")
     */
    private $exclusive;

    /**
     * @var int
     *
     * @ORM\Column(name="isnew", type="integer")
     */
    private $isnew;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var int
     *
     * @ORM\Column(name="isdisplayonfront", type="integer")
     */
    private $isDisplayOnFront;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="VoucherPrograms", inversedBy="voucher")	 
     * @ORM\JoinColumn(name="program_id", referencedColumnName="id")
     */
    private $program;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Language", inversedBy="voucher")	
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     */
    private $language;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Currency", inversedBy="voucher")	 
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     */
    private $currency;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Network", inversedBy="voucher")	
     * @ORM\JoinColumn(name="network_id", referencedColumnName="id")
     */
    private $network;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime")
     */
    private $modified;

    /**
     * @ORM\ManyToMany(targetEntity="Shop", mappedBy="voucher", cascade={"persist"})    
     */
    private $shop;

    public function __construct()
    {
        #$this->program = new ArrayCollection();
        #$this->language = new ArrayCollection();
        #$this->currency = new ArrayCollection();
        #$this->network = new ArrayCollection();
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
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
     * Set refVoucherId.
     *
     * @param int $refVoucherId
     *
     * @return Voucher
     */
    public function setRefVoucherId($refVoucherId)
    {
        $this->refVoucherId = $refVoucherId;

        return $this;
    }

    /**
     * Get refVoucherId.
     *
     * @return int
     */
    public function getRefVoucherId()
    {
        return $this->refVoucherId;
    }

    /**
     * Set code.
     *
     * @param string $code
     *
     * @return Voucher
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set publishStartDate.
     *
     * @param \DateTime $publishStartDate
     *
     * @return Voucher
     */
    public function setPublishStartDate($publishStartDate)
    {
        $this->publishStartDate = $publishStartDate;

        return $this;
    }

    /**
     * Get publishStartDate.
     *
     * @return \DateTime
     */
    public function getPublishStartDate()
    {
        return $this->publishStartDate;
    }

    /**
     * Set publishEndDate.
     *
     * @param \DateTime $publishEndDate
     *
     * @return Voucher
     */
    public function setPublishEndDate($publishEndDate)
    {
        $this->publishEndDate = $publishEndDate;

        return $this;
    }

    /**
     * Get publishEndDate.
     *
     * @return \DateTime
     */
    public function getPublishEndDate()
    {
        return $this->publishEndDate;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Voucher
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set shortDescription.
     *
     * @param string $shortDescription
     *
     * @return Voucher
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription.
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Voucher
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set voucherTypeId.
     *
     * @param int $voucherTypeId
     *
     * @return Voucher
     */
    public function setVoucherTypeId($voucherTypeId)
    {
        $this->voucherTypeId = $voucherTypeId;

        return $this;
    }

    /**
     * Get voucherTypeId.
     *
     * @return int
     */
    public function getVoucherTypeId()
    {
        return $this->voucherTypeId;
    }

    /**
     * Set defaultTrackUri.
     *
     * @param string $defaultTrackUri
     *
     * @return Voucher
     */
    public function setDefaultTrackUri($defaultTrackUri)
    {
        $this->defaultTrackUri = $defaultTrackUri;

        return $this;
    }

    /**
     * Get defaultTrackUri.
     *
     * @return string
     */
    public function getDefaultTrackUri()
    {
        return $this->defaultTrackUri;
    }

    /**
     * Set siteSpecific.
     *
     * @param int $siteSpecific
     *
     * @return Voucher
     */
    public function setSiteSpecific($siteSpecific)
    {
        $this->siteSpecific = $siteSpecific;

        return $this;
    }

    /**
     * Get siteSpecific.
     *
     * @return int
     */
    public function getSiteSpecific()
    {
        return $this->siteSpecific;
    }

    /**
     * Set landingUrl.
     *
     * @param string $landingUrl
     *
     * @return Voucher
     */
    public function setLandingUrl($landingUrl)
    {
        $this->landingUrl = $landingUrl;

        return $this;
    }

    /**
     * Get landingUrl.
     *
     * @return string
     */
    public function getLandingUrl()
    {
        return $this->landingUrl;
    }

    /**
     * Set discountAmount.
     *
     * @param string $discountAmount
     *
     * @return Voucher
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    /**
     * Get discountAmount.
     *
     * @return string
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * Set isPercentage.
     *
     * @param int $isPercentage
     *
     * @return Voucher
     */
    public function setIsPercentage($isPercentage)
    {
        $this->isPercentage = $isPercentage;

        return $this;
    }

    /**
     * Get isPercentage.
     *
     * @return int
     */
    public function getIsPercentage()
    {
        return $this->isPercentage;
    }

    /**
     * Set publisherInfo.
     *
     * @param string $publisherInfo
     *
     * @return Voucher
     */
    public function setPublisherInfo($publisherInfo)
    {
        $this->publisherInfo = $publisherInfo;

        return $this;
    }

    /**
     * Get publisherInfo.
     *
     * @return string
     */
    public function getPublisherInfo()
    {
        return $this->publisherInfo;
    }

    /**
     * Set exclusive.
     *
     * @param int $exclusive
     *
     * @return Voucher
     */
    public function setExclusive($exclusive)
    {
        $this->exclusive = $exclusive;

        return $this;
    }

    /**
     * Get exclusive.
     *
     * @return int
     */
    public function getExclusive()
    {
        return $this->exclusive;
    }


    /**
     * Set isnew.
     *
     * @param int $isnew
     *
     * @return Voucher
     */
    public function setIsNew($isnew)
    {
        $this->isnew = $isnew;

        return $this;
    }

    /**
     * Get isnew.
     *
     * @return int
     */
    public function getIsNew()
    {
        return $this->isnew;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Voucher
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
     * @return Voucher
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

    /**
     * Set languageId.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Language $languageId
     *
     * @return Voucher
     */
    public function setLanguageId(\iFlair\LetsBonusAdminBundle\Entity\Language $languageId = null)
    {
        $this->languageId = $languageId;

        return $this;
    }

    /**
     * Get languageId.
     *
     * @return \iFlair\LetsBonusAdminBundle\Entity\Language
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }

    /**
     * Set currencyId.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Currency $currencyId
     *
     * @return Voucher
     */
    public function setCurrencyId(\iFlair\LetsBonusAdminBundle\Entity\Currency $currencyId = null)
    {
        $this->currencyId = $currencyId;

        return $this;
    }

    /**
     * Get currencyId.
     *
     * @return \iFlair\LetsBonusAdminBundle\Entity\Currency
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    /**
     * Set program.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms $program
     *
     * @return Voucher
     */
    public function setProgram(\iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms $program = null)
    {
        $this->program = $program;

        return $this;
    }

    /**
     * Get program.
     *
     * @return \iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * Set language.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Language $language
     *
     * @return Voucher
     */
    public function setLanguage(\iFlair\LetsBonusAdminBundle\Entity\Language $language = null)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language.
     *
     * @return \iFlair\LetsBonusAdminBundle\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set currency.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Currency $currency
     *
     * @return Voucher
     */
    public function setCurrency(\iFlair\LetsBonusAdminBundle\Entity\Currency $currency = null)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency.
     *
     * @return \iFlair\LetsBonusAdminBundle\Entity\Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set network.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Network $network
     *
     * @return Voucher
     */
    public function setNetwork(\iFlair\LetsBonusAdminBundle\Entity\Network $network = null)
    {
        $this->network = $network;

        return $this;
    }

    /**
     * Get network.
     *
     * @return \iFlair\LetsBonusAdminBundle\Entity\Network
     */
    public function getNetwork()
    {
        return $this->network;
    }

    /**
     * Set status.
     *
     * @param int $status
     *
     * @return Voucher
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set isDisplayOnFront.
     *
     * @param string $isDisplayOnFront
     *
     * @return Voucher
     */
    public function setIsDisplayOnFront($isDisplayOnFront)
    {
        $this->isDisplayOnFront = $isDisplayOnFront;

        return $this;
    }

    /**
     * Get isDisplayOnFront.
     *
     * @return string
     */
    public function getIsDisplayOnFront()
    {
        return $this->isDisplayOnFront;
    }

    /**
     * Add shop.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Shop $shop
     *
     * @return Voucher
     */
    public function addShop(\iFlair\LetsBonusAdminBundle\Entity\Shop $shop)
    {
        $this->shop[] = $shop;

        return $this;
    }

    /**
     * Remove shop.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Shop $shop
     */
    public function removeShop(\iFlair\LetsBonusAdminBundle\Entity\Shop $shop)
    {
        $this->shop->removeElement($shop);
    }

    /**
     * Get shop.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShop()
    {
        return $this->shop;
    }
}
