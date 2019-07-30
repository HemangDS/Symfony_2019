<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Shop.
 *
 * @ORM\Table(name="lb_shop")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\ShopRepository")
 */
class Shop
{
    const SHOP_DEACTIVATED = 0;
    const SHOP_ACTIVATED = 1;
    const OFFER_ACTIVATED = 1;
    const SHOP_EDITORIAL = 2;
    const SHOP_MARKETING = 3;
    const SHOP_HIGHLIGHTED_HOME = 1;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Companies", inversedBy="shop")
     */
    private $companies;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="text")
     */
    private $keywords;

    /**
     * @ORM\ManyToOne(targetEntity="Network", inversedBy="shop")    
     */
    private $network;

    /**
     * @ORM\ManyToOne(targetEntity="networkCredentials", inversedBy="shop")
     */
    private $networkCredentials;

    /**
     * @ORM\ManyToOne(targetEntity="VoucherPrograms", inversedBy="shop")
     */
    private $vprogram;

    /**
     * @ORM\ManyToMany(targetEntity="Voucher", inversedBy="shop")
     * @ORM\JoinTable(name="lb_shop_voucher")
     */
    private $voucher;

    /**
     * @var string
     *
     * @ORM\Column(name="programId", type="string", length=255, nullable=true)
     */
    private $programId;

    /**
     * @var string
     *
     * @ORM\Column(name="urlAffiliate", type="string", length=255)
     */
    private $urlAffiliate;

    /**
     *@var bool
     *
     * @ORM\Column(name="exclusive", type="boolean", nullable=false)
     */
    private $exclusive;

    /**
     * @var int
     *
     * @ORM\Column(name="daysValidateConfirmation", type="integer")
     */
    private $daysValidateConfirmation;

    /**
     * @var int
     *
     * @ORM\Column(name="highlightedHome", type="integer")
     */
    private $highlightedHome;

    /**
     * @var int
     *
     * @ORM\Column(name="highlightedOffer", type="integer")
     */
    private $highlightedOffer;

    /**
     * @var int
     *
     * @ORM\Column(name="shopStatus", type="integer")
     */
    private $shopStatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startDate", type="datetime")
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endDate", type="datetime")
     */
    private $endDate;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    private $image;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    private $tabImage;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    private $categoryImage;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    private $newsletterImage;

     /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    private $highlineofferImage;


    /**
     * @ORM\ManyToMany(targetEntity="parentCategory", inversedBy="shop")
     * @ORM\JoinTable(name="lb_shop_parent_category",
     *      joinColumns={@ORM\JoinColumn(name="shop_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="parent_category_id", referencedColumnName="id")}
     *      )
     */
    private $parentCategory;

    /**
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="shop")
     * @ORM\JoinTable(name="lb_shop_category")
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="childCategory", inversedBy="shop")
     * @ORM\JoinTable(name="lb_shop_child_category",
     *      joinColumns={@ORM\JoinColumn(name="shop_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="child_category_id", referencedColumnName="id")}
     *  )
     */
    private $childCategory;

    /**
     * @ORM\ManyToMany(targetEntity="Collection", inversedBy="shop")
     * @ORM\JoinTable(name="lb_shop_collection")
     */
    private $collections;

    /**
     * @var string
     *
     * @ORM\Column(name="internalNotes", type="text", nullable=true)
     */
    private $internalNotes;

    /**
     * @ORM\ManyToMany(targetEntity="cashbackSettings", mappedBy="shop")
     */
    private $cashbackSettings;

    /**
     * @ORM\OneToMany(targetEntity="cashbackTransactions", mappedBy="shopId")
     */
    private $cashbackTransactions;

    /**
     * @ORM\OneToMany(targetEntity="shopHistory", mappedBy="shop")
     */
    private $shopHistory;

    /**
     * @ORM\OneToMany(targetEntity="Promo", mappedBy="shop")
     */
    private $promo;

    /**
     * @var string
     *
     * @ORM\Column(name="offers", type="string")
     */
    private $offers;

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
     * @ORM\OneToMany(targetEntity="AddtoFev", mappedBy="shopId")
     */
    private $addtofav;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="shop")
     * @ORM\Column(name="administrator", type="integer")
     */
    private $administrator;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="introduction", type="text", nullable=true)
     */
    private $introduction;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="tearms", type="text", nullable=true)
     */
    private $tearms;

    /**
     * @var string
     *
     * @ORM\Column(name="cashbackPrice", type="string", nullable=true)
     */
    private $cashbackPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="cashbackPercentage", type="string", nullable=true)
     */
    private $cashbackPercentage;

    /**
     * @var string
     *
     * @ORM\Column(name="letsBonusPercentage", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $letsBonusPercentage;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Tags", inversedBy="shop")
     * @ORM\JoinColumn(name="tag", referencedColumnName="id")     
     */
    private $tag;

    /**
     * @var string
     *
     * @ORM\Column(name="prevLabelCrossedOut", type="string", length=255, nullable=true)
     */
    private $prevLabelCrossedOut;

    /**
     * @var string
     *
     * @ORM\Column(name="shippingInfo", type="string", length=255, nullable=true)
     */
    private $shippingInfo;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="shopVariation", mappedBy="shop", cascade={"persist", "remove"})
     */
    private $shopVariation;

    /**
     * @ORM\OneToMany(targetEntity="TransactionalQueueMail", mappedBy="shop")
     */
    private $transactionalQueueMail;

    /**
     * @ORM\OneToMany(targetEntity="iFlair\LetsBonusFrontBundle\Entity\Review", mappedBy="shopId")
     */
    private $review;

    public function __toString()
    {
        return strval($this->id);
    }

    public function __construct()
    {
        $this->shopVariation = new ArrayCollection();
        $this->voucher = new ArrayCollection();
        $this->promo = new ArrayCollection();
        $this->shopHistory = new ArrayCollection();
        $this->cashbackTransactions = new ArrayCollection();
        $this->cashbackSettings = new ArrayCollection();
        $this->parentCategory = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->childCategory = new ArrayCollection();
        $this->collections = new ArrayCollection();
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
        //$this->network = new ArrayCollection();
        $this->addtofav = new ArrayCollection();
    }

    public function getAddToFav()
    {
        return $this->addtofav;
    }

    public function getPromo()
    {
        return $this->promo;
    }

    public function getshopHistory()
    {
        return $this->shopHistory;
    }

    public function getCashbackTransactions()
    {
        return $this->cashbackTransactions;
    }

    public function getCashbackSettings()
    {
        return $this->cashbackSettings;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set companies.
     *
     * @param int $companies
     *
     * @return Shops
     */
    public function setCompanies($companies)
    {
        $this->companies = $companies;

        return $this;
    }

    /**
     * Get companies.
     *
     * @return int
     */
    public function getCompanies()
    {
        return $this->companies;
    }

    /**
     * Set keywords.
     *
     * @param string $keywords
     *
     * @return Shop
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords.
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    public function setNetwork(Network $network)
    {
        $this->network = $network;
    }

    public function getNetwork()
    {
        return $this->network;
    }

    /**
     * Set programId.
     *
     * @param string $programId
     *
     * @return Shop
     */
    public function setVprogram(VoucherPrograms $vprogram)
    {
        $this->vprogram = $vprogram;

        return $this;
    }

    /**
     * Get programId.
     *
     * @return string
     */
    public function getVprogram()
    {
        return $this->vprogram;
    }

    /**
     * Set networkCredentials.
     *
     * @param string $networkCredentials
     *
     * @return Shop
     */
    public function setNetworkCredentials(networkCredentials $networkCredentials)
    {
        $this->networkCredentials = $networkCredentials;

        return $this;
    }

    /**
     * Get networkCredentials.
     *
     * @return string
     */
    public function getNetworkCredentials()
    {
        return $this->networkCredentials;
    }
    /**
     * Set exclusive.
     *
     * @param bool $exclusive
     *
     * @return Shop
     */
    public function setExclusive($exclusive)
    {
        $this->exclusive = $exclusive;

        return $this;
    }

    /**
     * Get exclusive.
     *
     * @return bool
     */
    public function getExclusive()
    {
        return $this->exclusive;
    }

    /**
     * Set urlAffiliate.
     *
     * @param string $urlAffiliate
     *
     * @return Shop
     */
    public function setUrlAffiliate($urlAffiliate)
    {
        $this->urlAffiliate = $urlAffiliate;

        return $this;
    }

    /**
     * Get urlAffiliate.
     *
     * @return string
     */
    public function getUrlAffiliate()
    {
        return $this->urlAffiliate;
    }

    /**
     * Set daysValidateConfirmation.
     *
     * @param int $daysValidateConfirmation
     *
     * @return Shop
     */
    public function setDaysValidateConfirmation($daysValidateConfirmation)
    {
        $this->daysValidateConfirmation = $daysValidateConfirmation;

        return $this;
    }

    /**
     * Get daysValidateConfirmation.
     *
     * @return int
     */
    public function getDaysValidateConfirmation()
    {
        return $this->daysValidateConfirmation;
    }

    /**
     * Set highlightedHome.
     *
     * @param int $highlightedHome
     *
     * @return Shop
     */
    public function setHighlightedHome($highlightedHome)
    {
        $this->highlightedHome = $highlightedHome;

        return $this;
    }

    /**
     * Get highlightedHome.
     *
     * @return int
     */
    public function getHighlightedHome()
    {
        return $this->highlightedHome;
    }

    /**
     * Set shopStatus.
     *
     * @param int $shopStatus
     *
     * @return Shop
     */
    public function setShopStatus($shopStatus)
    {
        $this->shopStatus = $shopStatus;

        return $this;
    }

    /**
     * Get shopStatus.
     *
     * @return int
     */
    public function getShopStatus()
    {
        return $this->shopStatus;
    }

    /**
     * Set startDate.
     *
     * @param \DateTime $startDate
     *
     * @return Shop
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate.
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate.
     *
     * @param \DateTime $endDate
     *
     * @return Shop
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate.
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set image.
     *
     * @param string $image
     *
     * @return Shop
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set tabImage.
     *
     * @param string $tabImage
     *
     * @return Shop
     */
    public function setTabImage($tabImage)
    {
        $this->tabImage = $tabImage;

        return $this;
    }

    /**
     * Get tabImage.
     *
     * @return string
     */
    public function getTabImage()
    {
        return $this->tabImage;
    }

    /**
     * Set categoryImage.
     *
     * @param string $categoryImage
     *
     * @return Shop
     */
    public function setCategoryImage($categoryImage)
    {
        $this->categoryImage = $categoryImage;

        return $this;
    }

    /**
     * Get categoryImage.
     *
     * @return string
     */
    public function getCategoryImage()
    {
        return $this->categoryImage;
    }

    /**
     * Set newsletterImage.
     *
     * @param string $newsletterImage
     *
     * @return Shop
     */
    public function setNewsletterImage($newsletterImage)
    {
        $this->newsletterImage = $newsletterImage;

        return $this;
    }

    /**
     * Get newsletterImage.
     *
     * @return string
     */
    public function getNewsletterImage()
    {
        return $this->newsletterImage;
    }

      /**
     * Set highlineofferImage.
     *
     * @param string $highlineofferImage
     *
     * @return Shop
     */
    public function setHighlineofferImage($highlineofferImage)
    {
        $this->highlineofferImage = $highlineofferImage;

        return $this;
    }

    /**
     * Get highlineofferImage.
     *
     * @return string
     */
    public function getHighlineofferImage()
    {
        return $this->highlineofferImage;
    }

    public function setParentCategory(parentCategory $parentCategory)
    {
        $this->parentCategory = $parentCategory;
    }

    public function getParentCategory()
    {
        return $this->parentCategory;
    }

    public function setCategories(Category $categories)
    {
        $this->categories = $categories;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function setChildCategory(childCategory $childCategory)
    {
        $this->childCategory = $childCategory;
    }

    public function getChildCategory()
    {
        return $this->childCategory;
    }

    /**
     * Set collections.
     *
     * @param string $collections
     *
     * @return Shop
     */
    public function setCollections(Collection $collections)
    {
        $this->collections = $collections;

        return $this;
    }

    /**
     * Get collections.
     *
     * @return string
     */
    public function getCollections()
    {
        return $this->collections;
    }

    /**
     * Set internalNotes.
     *
     * @param string $internalNotes
     *
     * @return Shop
     */
    public function setInternalNotes($internalNotes)
    {
        $this->internalNotes = $internalNotes;

        return $this;
    }

    /**
     * Get internalNotes.
     *
     * @return string
     */
    public function getInternalNotes()
    {
        return $this->internalNotes;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return shopHistory
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
     * @return shopHistory
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
     * Set offers.
     *
     * @param string $offers
     *
     * @return Shop
     */
    public function setOffers($offers)
    {
        $this->offers = $offers;

        return $this;
    }

    /**
     * Get offers.
     *
     * @return string
     */
    public function getOffers()
    {
        return $this->offers;
    }

    /**
     * Set voucher.
     *
     * @param string $voucher
     *
     * @return Shop
     */
    public function setVoucher(Voucher $voucher)
    {
        $this->voucher = $voucher;

        return $this;
    }

    /**
     * Get voucher.
     *
     * @return string
     */
    public function getVoucher()
    {
        return $this->voucher;
    }

    /**
     * Add voucher.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Voucher $voucher
     *
     * @return Shop
     */
    public function addVoucher(Voucher $voucher)
    {
        $this->voucher[] = $voucher;

        return $this;
    }

    /**
     * Remove voucher.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Voucher $voucher
     */
    public function removeVoucher(Voucher $voucher)
    {
        $this->voucher->removeElement($voucher);
    }

    /**
     * Add collection.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Collection $collection
     *
     * @return Shop
     */
    public function addCollection(\iFlair\LetsBonusAdminBundle\Entity\Collection $collection)
    {
        $this->collections[] = $collection;

        return $this;
    }

    /**
     * Remove collection.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Collection $collection
     */
    public function removeCollection(\iFlair\LetsBonusAdminBundle\Entity\Collection $collection)
    {
        $this->collections->removeElement($collection);
    }

    /**
     * Add cashbackSetting.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\cashbackSettings $cashbackSetting
     *
     * @return Shop
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
     * @return Shop
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
     * Add shopHistory.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\shopHistory $shopHistory
     *
     * @return Shop
     */
    public function addShopHistory(\iFlair\LetsBonusAdminBundle\Entity\shopHistory $shopHistory)
    {
        $this->shopHistory[] = $shopHistory;

        return $this;
    }

    /**
     * Remove shopHistory.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\shopHistory $shopHistory
     */
    public function removeShopHistory(\iFlair\LetsBonusAdminBundle\Entity\shopHistory $shopHistory)
    {
        $this->shopHistory->removeElement($shopHistory);
    }

    /**
     * Add promo.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Promo $promo
     *
     * @return Shop
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
     * Set programId.
     *
     * @param string $programId
     *
     * @return Shop
     */
    public function setProgramId($programId)
    {
        $this->programId = $programId;

        return $this;
    }

    /**
     * Get programId.
     *
     * @return string
     */
    public function getProgramId()
    {
        return $this->programId;
    }

    /**
     * Add parentCategory.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\parentCategory $parentCategory
     *
     * @return Shop
     */
    public function addParentCategory(\iFlair\LetsBonusAdminBundle\Entity\parentCategory $parentCategory)
    {
        $this->parentCategory[] = $parentCategory;

        return $this;
    }

    /**
     * Remove parentCategory.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\parentCategory $parentCategory
     */
    public function removeParentCategory(\iFlair\LetsBonusAdminBundle\Entity\parentCategory $parentCategory)
    {
        $this->parentCategory->removeElement($parentCategory);
    }

    /**
     * Add category.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Category $category
     *
     * @return Shop
     */
    public function addCategory(\iFlair\LetsBonusAdminBundle\Entity\Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Category $category
     */
    public function removeCategory(\iFlair\LetsBonusAdminBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Add childCategory.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\childCategory $childCategory
     *
     * @return Shop
     */
    public function addChildCategory(\iFlair\LetsBonusAdminBundle\Entity\childCategory $childCategory)
    {
        $this->childCategory[] = $childCategory;

        return $this;
    }

    /**
     * Remove childCategory.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\childCategory $childCategory
     */
    public function removeChildCategory(\iFlair\LetsBonusAdminBundle\Entity\childCategory $childCategory)
    {
        $this->childCategory->removeElement($childCategory);
    }

    /**
     * Add network.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Network $network
     *
     * @return Shop
     */
    public function addNetwork(\iFlair\LetsBonusAdminBundle\Entity\Network $network)
    {
        $this->network[] = $network;

        return $this;
    }

    /**
     * Remove network.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Network $network
     */
    public function removeNetwork(\iFlair\LetsBonusAdminBundle\Entity\Network $network)
    {
        $this->network->removeElement($network);
    }

    /**
     * Add addtofav.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\AddtoFev $addtofav
     *
     * @return Shop
     */
    public function addAddtofav(\iFlair\LetsBonusAdminBundle\Entity\AddtoFev $addtofav)
    {
        $this->addtofav[] = $addtofav;

        return $this;
    }

    /**
     * Remove addtofav.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\AddtoFev $addtofav
     */
    public function removeAddtofav(\iFlair\LetsBonusAdminBundle\Entity\AddtoFev $addtofav)
    {
        $this->addtofav->removeElement($addtofav);
    }

    /**
     * Set administrator.
     *
     * @param int $administrator
     *
     * @return Shop
     */
    public function setAdministrator($administrator)
    {
        $this->administrator = $administrator;

        return $this;
    }

    /**
     * Get administrator.
     *
     * @return int
     */
    public function getAdministrator()
    {
        return $this->administrator;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Shop
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
     * Set url.
     *
     * @param string $url
     *
     * @return Shop
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set introduction.
     *
     * @param string $introduction
     *
     * @return Shop
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;

        return $this;
    }

    /**
     * Get introduction.
     *
     * @return string
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Shop
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
     * Set tearms.
     *
     * @param string $tearms
     *
     * @return Shop
     */
    public function setTearms($tearms)
    {
        $this->tearms = $tearms;

        return $this;
    }

    /**
     * Get tearms.
     *
     * @return string
     */
    public function getTearms()
    {
        return $this->tearms;
    }

    /**
     * Set cashbackPrice.
     *
     * @param string $cashbackPrice
     *
     * @return Shop
     */
    public function setCashbackPrice($cashbackPrice)
    {
        $this->cashbackPrice = $cashbackPrice;

        return $this;
    }

    /**
     * Get cashbackPrice.
     *
     * @return string
     */
    public function getCashbackPrice()
    {
        return $this->cashbackPrice;
    }

    /**
     * Set cashbackPercentage.
     *
     * @param string $cashbackPercentage
     *
     * @return Shop
     */
    public function setCashbackPercentage($cashbackPercentage)
    {
        $this->cashbackPercentage = $cashbackPercentage;

        return $this;
    }

    /**
     * Get cashbackPercentage.
     *
     * @return string
     */
    public function getCashbackPercentage()
    {
        return $this->cashbackPercentage;
    }

    /**
     * Set letsBonusPercentage.
     *
     * @param string $letsBonusPercentage
     *
     * @return Shop
     */
    public function setLetsBonusPercentage($letsBonusPercentage)
    {
        $this->letsBonusPercentage = $letsBonusPercentage;

        return $this;
    }

    /**
     * Get letsBonusPercentage.
     *
     * @return string
     */
    public function getLetsBonusPercentage()
    {
        return $this->letsBonusPercentage;
    }

    /**
     * Set tag.
     *
     * @param int $tag
     *
     * @return Shop
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag.
     *
     * @return int
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set prevLabelCrossedOut.
     *
     * @param string $prevLabelCrossedOut
     *
     * @return Shop
     */
    public function setPrevLabelCrossedOut($prevLabelCrossedOut)
    {
        $this->prevLabelCrossedOut = $prevLabelCrossedOut;

        return $this;
    }

    /**
     * Get prevLabelCrossedOut.
     *
     * @return string
     */
    public function getPrevLabelCrossedOut()
    {
        return $this->prevLabelCrossedOut;
    }

    /**
     * Set shippingInfo.
     *
     * @param string $shippingInfo
     *
     * @return Shop
     */
    public function setShippingInfo($shippingInfo)
    {
        $this->shippingInfo = $shippingInfo;

        return $this;
    }

    /**
     * Get shippingInfo.
     *
     * @return string
     */
    public function getShippingInfo()
    {
        return $this->shippingInfo;
    }

    /**
     * Set shopVariation.
     *
     * @param string $shopVariation
     *
     * @return Shop
     */
    public function setShopVariation($shopVariation)
    {
        $this->shopVariation = $shopVariation;

        return $this;
    }

    /**
     * Get shopVariation.
     *
     * @return string
     */
    public function getShopVariation()
    {
        return $this->shopVariation;
    }

    public function addShopVariation(shopVariation $shopVariation)
    {
        $this->shopVariation->add($shopVariation);
        $shopVariation->setShop($this);  // The important line !!!!
        /*$this->variation[] = $variation;

        return $this;*/
    }

    public function removeShopVariation(shopVariation $shopVariation)
    {
        $this->shopVariation->removeElement($shopVariation);
        $shopVariation->setShop(null);
    }

    /**
     * Add transactionalQueueMail.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\TransactionalQueueMail $transactionalQueueMail
     *
     * @return Shop
     */
    public function addTransactionalQueueMail(\iFlair\LetsBonusAdminBundle\Entity\TransactionalQueueMail $transactionalQueueMail)
    {
        $this->transactionalQueueMail[] = $transactionalQueueMail;

        return $this;
    }

    /**
     * Remove transactionalQueueMail.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\TransactionalQueueMail $transactionalQueueMail
     */
    public function removeTransactionalQueueMail(\iFlair\LetsBonusAdminBundle\Entity\TransactionalQueueMail $transactionalQueueMail)
    {
        $this->transactionalQueueMail->removeElement($transactionalQueueMail);
    }

    /**
     * Get transactionalQueueMail.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransactionalQueueMail()
    {
        return $this->transactionalQueueMail;
    }

    /**
     * Add review.
     *
     * @param \iFlair\LetsBonusFrontBundle\Entity\Review $review
     *
     * @return Shop
     */
    public function addReview(\iFlair\LetsBonusFrontBundle\Entity\Review $review)
    {
        $this->review[] = $review;

        return $this;
    }

    /**
     * Remove review.
     *
     * @param \iFlair\LetsBonusFrontBundle\Entity\Review $review
     */
    public function removeReview(\iFlair\LetsBonusFrontBundle\Entity\Review $review)
    {
        $this->review->removeElement($review);
    }

    /**
     * Get review.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set highlightedOffer
     *
     * @param integer $highlightedOffer
     *
     * @return Shop
     */
    public function setHighlightedOffer($highlightedOffer)
    {
        $this->highlightedOffer = $highlightedOffer;

        return $this;
    }

    /**
     * Get highlightedOffer
     *
     * @return integer
     */
    public function getHighlightedOffer()
    {
        return $this->highlightedOffer;
    }
}
