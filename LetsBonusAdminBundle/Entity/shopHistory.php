<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use iFlair\LetsBonusFrontBundle\Entity\Review;

/**
 * shopHistory.
 *
 * @ORM\Table(name="lb_shop_history")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\shopHistoryRepository")
 */
class shopHistory
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
     * @var int
     * @ORM\ManyToOne(targetEntity="Shop", inversedBy="shopHistory")
     * @ORM\JoinColumn(name="shop", referencedColumnName="id")
     */
    private $shop;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="shopHistory")
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
     * @var string
     *
     * @ORM\Column(name="urlAffiliate", type="string", length=255, nullable=true)
     */
    private $urlAffiliate;

    /**
     * @var int
     *
     * @ORM\Column(name="show_on_como_functiona", type="integer", nullable=true)
     */
    private $show_on_como_functiona;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startDate", type="datetime")
     */
    private $startDate;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Tags", inversedBy="shopHistory")
     * @ORM\JoinColumn(name="tag", referencedColumnName="id", nullable=true)
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
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Variation", mappedBy="shopHistory", cascade={"persist", "remove"})
     */
    private $variation;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AddtoFev", mappedBy="shopHistoryId")
     */
    private $addtofav;

    /**
     * @ORM\OneToMany(targetEntity="cashbackTransactions", mappedBy="shopHistory")
     */
    private $cashbackTransactions;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="LetsBonusTransactions", mappedBy="shopHistory")
     */
    private $letsbonusTransactions;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="TransactionalQueueMail", mappedBy="shopHistory")
     */
    private $transactionalQueueMail;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="iFlair\LetsBonusFrontBundle\Entity\Review", mappedBy="shopHistoryId")
     */
    private $review;

    public function __toString()
    {
        return (string) $this->id;
    }

    public function __construct()
    {
        $this->variation = new ArrayCollection();
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
        $this->addtofav = new ArrayCollection();
    }

    public function getAddToFav()
    {
        return $this->addtofav;
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
     * @return shopHistory
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set shop.
     *
     * @param int $shop
     *
     * @return shopHistory
     */
    public function setShop($shop)
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * Get shop.
     *
     * @return int
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * Set administrator.
     *
     * @param int $administrator
     *
     * @return shopHistory
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
     * @return shopHistory
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
     * @return shopHistory
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
     * @return shopHistory
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
     * @return shopHistory
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
     * @return shopHistory
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
     * @return shopHistory
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
     * @return shopHistory
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
     * @return shopHistory
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
     * Set urlAffiliate.
     *
     * @param string $urlAffiliate
     *
     * @return shopHistory
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
     * Set startDate.
     *
     * @param \DateTime $startDate@param \DateTime $startDate
     *
     * @return shopHistory
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
     * Set tag.
     *
     * @param int $tag
     *
     * @return shopHistory
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
     * @return shopHistory
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
     * @return shopHistory
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
     * Set variation.
     *
     * @param string $variation
     *
     * @return ShopHistory
     */
    public function setVariation($variation)
    {
        $this->variation = $variation;

        return $this;
    }

    /**
     * Get variation.
     *
     * @return string
     */
    public function getVariation()
    {
        return $this->variation;
    }

    public function addVariation(Variation $variation)
    {
        $this->variation->add($variation);
        $variation->setShopHistory($this);  // The important line !!!!
        /*$this->variation[] = $variation;

        return $this;*/
    }

    public function removeVariation(Variation $variation)
    {
        $this->variation->removeElement($variation);
        $variation->setShopHistory(null);
    }

    /**
     * Add addtofav.
     *
     * @param AddtoFev $addtofav
     *
     * @return shopHistory
     */
    public function addAddtofav(AddtoFev $addtofav)
    {
        $this->addtofav[] = $addtofav;

        return $this;
    }

    /**
     * Remove addtofav.
     *
     * @param AddtoFev $addtofav
     */
    public function removeAddtofav(AddtoFev $addtofav)
    {
        $this->addtofav->removeElement($addtofav);
    }

    /**
     * Add cashbackTransaction.
     *
     * @param cashbackTransactions $cashbackTransaction
     *
     * @return shopHistory
     */
    public function addCashbackTransaction(cashbackTransactions $cashbackTransaction)
    {
        $this->cashbackTransactions[] = $cashbackTransaction;

        return $this;
    }

    /**
     * Remove cashbackTransaction.
     *
     * @param cashbackTransactions $cashbackTransaction
     */
    public function removeCashbackTransaction(cashbackTransactions $cashbackTransaction)
    {
        $this->cashbackTransactions->removeElement($cashbackTransaction);
    }

    /**
     * Get cashbackTransactions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCashbackTransactions()
    {
        return $this->cashbackTransactions;
    }

    /**
     * Add letsbonusTransaction.
     *
     * @param LetsBonusTransactions $letsbonusTransaction
     *
     * @return shopHistory
     */
    public function addLetsbonusTransaction(LetsBonusTransactions $letsbonusTransaction)
    {
        $this->letsbonusTransactions[] = $letsbonusTransaction;

        return $this;
    }

    /**
     * Remove letsbonusTransaction.
     *
     * @param LetsBonusTransactions $letsbonusTransaction
     */
    public function removeLetsbonusTransaction(LetsBonusTransactions $letsbonusTransaction)
    {
        $this->letsbonusTransactions->removeElement($letsbonusTransaction);
    }

    /**
     * Get letsbonusTransactions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLetsbonusTransactions()
    {
        return $this->letsbonusTransactions;
    }

    /**
     * Add transactionalQueueMail.
     *
     * @param TransactionalQueueMail $transactionalQueueMail
     *
     * @return shopHistory
     */
    public function addTransactionalQueueMail(TransactionalQueueMail $transactionalQueueMail)
    {
        $this->transactionalQueueMail[] = $transactionalQueueMail;

        return $this;
    }

    /**
     * Remove transactionalQueueMail.
     *
     * @param TransactionalQueueMail $transactionalQueueMail
     */
    public function removeTransactionalQueueMail(TransactionalQueueMail $transactionalQueueMail)
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
     * @param Review $review
     *
     * @return shopHistory
     */
    public function addReview(Review $review)
    {
        $this->review[] = $review;

        return $this;
    }

    /**
     * Remove review.
     *
     * @param Review $review
     */
    public function removeReview(Review $review)
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
     * Set show_on_como_functiona.
     *
     * @param string $show_on_como_functiona
     *
     * @return shopHistory
     */
    public function setShowOnComoFunctiona($show_on_como_functiona)
    {
        $this->show_on_como_functiona = $show_on_como_functiona;

        return $this;
    }

    /**
     * Get show_on_como_functiona.
     *
     * @return string
     */
    public function getShowOnComoFunctiona()
    {
        return $this->show_on_como_functiona;
    }
}
