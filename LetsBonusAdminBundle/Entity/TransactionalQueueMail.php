<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * TransactionalQueueMail.
 *
 * @ORM\Table(name="lb_transactional_queue_mail")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\TransactionalQueueMailRepository")
 */
class TransactionalQueueMail
{
    const PURCHASE_DONE = 'purchase-done';
    const PURCHASE_DENIED = 'purchase-denied';
    const PURCHASE_CONFIRMED = 'purchase-confirmed';
    const STATUS_TYPE_PENDING = 'PENDING';
    const STATUS_TYPE_CONFIRMED = 'CONFIRMED';
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Shop", inversedBy="transactionalQueueMail")
     */
    private $shop;

    /**
     * @ORM\ManyToOne(targetEntity="shopHistory", inversedBy="transactionalQueueMail")
     */
    private $shopHistory;

    /**
     * @var string
     *
     * @ORM\Column(name="cashbacktransaction_id", type="string", length=255)
     */
    private $cashbacktransactionId;

    /**
     * @var int
     *
     * @ORM\Column(name="idClient", type="integer")
     */
    private $idClient;

    /**
     * @var string
     *
     * @ORM\Column(name="isoCode", type="string", length=255)
     */
    private $isoCode;

    /**
     * @var string
     *
     * @ORM\Column(name="mailType", type="string", length=255)
     */
    private $mailType;

    /**
     * @var string
     *
     * @ORM\Column(name="shopName", type="string", length=255)
     */
    private $shopName;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=10, scale=2)
     */
    private $total;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=255)
     */
    private $currency;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="purchaseDate", type="datetime")
     */
    private $purchaseDate;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sended_date", type="datetime")
     */
    private $sendedDate;

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

    public function __construct()
    {
        $this->shop = new ArrayCollection();
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
     * Set cashbacktransactionId.
     *
     * @param string $cashbacktransactionId
     *
     * @return TransactionalQueueMail
     */
    public function setCashbacktransactionId($cashbacktransactionId)
    {
        $this->cashbacktransactionId = $cashbacktransactionId;

        return $this;
    }

    /**
     * Get cashbacktransactionId.
     *
     * @return string
     */
    public function getCashbacktransactionId()
    {
        return $this->cashbacktransactionId;
    }

    /**
     * Set idClient.
     *
     * @param int $idClient
     *
     * @return TransactionalQueueMail
     */
    public function setIdClient($idClient)
    {
        $this->idClient = $idClient;

        return $this;
    }

    /**
     * Get idClient.
     *
     * @return int
     */
    public function getIdClient()
    {
        return $this->idClient;
    }

    /**
     * Set isoCode.
     *
     * @param string $isoCode
     *
     * @return TransactionalQueueMail
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
     * Set mailType.
     *
     * @param string $mailType
     *
     * @return TransactionalQueueMail
     */
    public function setMailType($mailType)
    {
        $this->mailType = $mailType;

        return $this;
    }

    /**
     * Get mailType.
     *
     * @return string
     */
    public function getMailType()
    {
        return $this->mailType;
    }

    /**
     * Set shopName.
     *
     * @param string $shopName
     *
     * @return TransactionalQueueMail
     */
    public function setShopName($shopName)
    {
        $this->shopName = $shopName;

        return $this;
    }

    /**
     * Get shopName.
     *
     * @return string
     */
    public function getShopName()
    {
        return $this->shopName;
    }

    /**
     * Set amount.
     *
     * @param string $amount
     *
     * @return TransactionalQueueMail
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount.
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set total.
     *
     * @param string $total
     *
     * @return TransactionalQueueMail
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total.
     *
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set currency.
     *
     * @param string $currency
     *
     * @return TransactionalQueueMail
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set purchaseDate.
     *
     * @param \DateTime $purchaseDate
     *
     * @return TransactionalQueueMail
     */
    public function setPurchaseDate($purchaseDate)
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    /**
     * Get purchaseDate.
     *
     * @return \DateTime
     */
    public function getPurchaseDate()
    {
        return $this->purchaseDate;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return TransactionalQueueMail
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set sendedDate.
     *
     * @param \DateTime $sendedDate
     *
     * @return TransactionalQueueMail
     */
    public function setSendedDate($sendedDate)
    {
        $this->sendedDate = $sendedDate;

        return $this;
    }

    /**
     * Get sendedDate.
     *
     * @return \DateTime
     */
    public function getSendedDate()
    {
        return $this->sendedDate;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return TransactionalQueueMail
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
     * @return TransactionalQueueMail
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
     * Set shop.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Shop $shop
     *
     * @return TransactionalQueueMail
     */
    public function setShop(\iFlair\LetsBonusAdminBundle\Entity\Shop $shop = null)
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * Get shop.
     *
     * @return \iFlair\LetsBonusAdminBundle\Entity\Shop
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * Set shopHistory.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\shopHistory $shopHistory
     *
     * @return TransactionalQueueMail
     */
    public function setShopHistory(\iFlair\LetsBonusAdminBundle\Entity\shopHistory $shopHistory = null)
    {
        $this->shopHistory = $shopHistory;

        return $this;
    }

    /**
     * Get shopHistory.
     *
     * @return \iFlair\LetsBonusAdminBundle\Entity\shopHistory
     */
    public function getShopHistory()
    {
        return $this->shopHistory;
    }
}
