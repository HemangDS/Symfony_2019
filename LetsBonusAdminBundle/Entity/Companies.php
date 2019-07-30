<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Companies.
 *
 * @ORM\Table(name="lb_companies")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\CompaniesRepository")
 */
class Companies
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Currency", inversedBy="companies")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="isoCode", type="string", length=255)
     */
    private $isoCode;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Language", inversedBy="companies")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="commonConditions", type="text")
     */
    private $commonConditions;

    /**
     * @var int
     *
     * @ORM\Column(name="hoursOffset", type="integer")
     */
    private $hoursOffset;

    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string", length=255)
     */
    private $timezone;

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
     * @ORM\OneToMany(targetEntity="cashbackSettings", mappedBy="companies")
     */
    private $cashbackSettings;

    /**
     * @ORM\OneToMany(targetEntity="cashbackTransactions", mappedBy="companyId")
     */
    private $cashbackTransactions;

    /**
     * @ORM\OneToMany(targetEntity="Shop", mappedBy="companies")
     */
    private $shop;

    /**
     * @ORM\OneToMany(targetEntity="Promo", mappedBy="companies")
     */
    private $promo;

    /**
     * @ORM\OneToMany(targetEntity="Settings", mappedBy="companies")
     */
    private $settings;

    /**
     * @ORM\OneToMany(targetEntity="VoucherTradeDoublerSiteToken", mappedBy="company")
     */
    private $voucherTradeDoublerSiteToken;

    public function __toString()
    {
        return strval($this->id);
    }

    public function __construct()
    {
        $this->promo = new ArrayCollection();
        $this->shop = new ArrayCollection();
        $this->cashbackTransactions = new ArrayCollection();
        $this->cashbackSettings = new ArrayCollection();
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
     * Set id.
     *
     * @param int $id
     *
     * @return Companies
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * @param int $currency
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
     * @return int
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
     * @param int $lang
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
     * @return int
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
     * @return Collection
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
     * @return Collection
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

    public function getCashbackSettings()
    {
        return $this->cashbackSettings;
    }

    public function getCashbackTransactions()
    {
        return $this->cashbackTransactions;
    }

    public function getShop()
    {
        return $this->shop;
    }

    public function getPromo()
    {
        return $this->promo;
    }

    /**
     * Add cashbackSetting.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\cashbackSettings $cashbackSetting
     *
     * @return Companies
     */
    public function addCashbackSetting(\iFlair\LetsBonusAdminBundle\Entity\cashbackSettings $cashbackSetting)
    {
        $this->cashbackSettings[] = $cashbackSetting;

        return $this;
    }

    /**
     * Remove cashbackSetting.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\cashbackSettings $cashbackSetting
     */
    public function removeCashbackSetting(\iFlair\LetsBonusAdminBundle\Entity\cashbackSettings $cashbackSetting)
    {
        $this->cashbackSettings->removeElement($cashbackSetting);
    }

    /**
     * Add cashbackTransaction.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\cashbackTransactions $cashbackTransaction
     *
     * @return Companies
     */
    public function addCashbackTransaction(\iFlair\LetsBonusAdminBundle\Entity\cashbackTransactions $cashbackTransaction)
    {
        $this->cashbackTransactions[] = $cashbackTransaction;

        return $this;
    }

    /**
     * Remove cashbackTransaction.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\cashbackTransactions $cashbackTransaction
     */
    public function removeCashbackTransaction(\iFlair\LetsBonusAdminBundle\Entity\cashbackTransactions $cashbackTransaction)
    {
        $this->cashbackTransactions->removeElement($cashbackTransaction);
    }

    /**
     * Add shop.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Shop $shop
     *
     * @return Companies
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
     * Add promo.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Promo $promo
     *
     * @return Companies
     */
    public function addPromo(\iFlair\LetsBonusAdminBundle\Entity\Promo $promo)
    {
        $this->promo[] = $promo;

        return $this;
    }

    /**
     * Remove promo.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Promo $promo
     */
    public function removePromo(\iFlair\LetsBonusAdminBundle\Entity\Promo $promo)
    {
        $this->promo->removeElement($promo);
    }

    /**
     * Add setting.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Settings $setting
     *
     * @return Companies
     */
    public function addSetting(\iFlair\LetsBonusAdminBundle\Entity\Settings $setting)
    {
        $this->settings[] = $setting;

        return $this;
    }

    /**
     * Remove setting.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Settings $setting
     */
    public function removeSetting(\iFlair\LetsBonusAdminBundle\Entity\Settings $setting)
    {
        $this->settings->removeElement($setting);
    }

    /**
     * Get settings.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Add voucherTradeDoublerSiteToken.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions $voucherTradeDoublerSiteToken
     *
     * @return Companies
     */
    public function addVoucherTradeDoublerSiteToken(\iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions $voucherTradeDoublerSiteToken)
    {
        $this->voucherTradeDoublerSiteToken[] = $voucherTradeDoublerSiteToken;

        return $this;
    }

    /**
     * Remove voucherTradeDoublerSiteToken.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions $voucherTradeDoublerSiteToken
     */
    public function removeVoucherTradeDoublerSiteToken(\iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions $voucherTradeDoublerSiteToken)
    {
        $this->voucherTradeDoublerSiteToken->removeElement($voucherTradeDoublerSiteToken);
    }

    /**
     * Get voucherTradeDoublerSiteToken.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoucherTradeDoublerSiteToken()
    {
        return $this->voucherTradeDoublerSiteToken;
    }
}
