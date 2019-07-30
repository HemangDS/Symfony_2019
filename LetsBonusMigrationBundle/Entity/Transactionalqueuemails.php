<?php

namespace iFlair\LetsBonusMigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transactionalqueuemails.
 *
 * @ORM\Table(name="transactionalqueuemails", indexes={@ORM\Index(name="n2_transactionalqueuemails", columns={"idClient"}), @ORM\Index(name="n1_transactionalqueuemails", columns={"shop_id"}), @ORM\Index(name="n3_transactionalqueuemails", columns={"shopshistory_id"}), @ORM\Index(name="n4_transactionalqueuemails", columns={"cashbacktransaction_id"})})
 * @ORM\Entity
 */
class Transactionalqueuemails
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="shop_id", type="integer", nullable=true)
     */
    private $shopId;

    /**
     * @var int
     *
     * @ORM\Column(name="shopshistory_id", type="integer", nullable=true)
     */
    private $shopshistoryId;

    /**
     * @var string
     *
     * @ORM\Column(name="cashbacktransaction_id", type="string", length=255, nullable=true)
     */
    private $cashbacktransactionId;

    /**
     * @var int
     *
     * @ORM\Column(name="idClient", type="integer", nullable=true)
     */
    private $idclient;

    /**
     * @var string
     *
     * @ORM\Column(name="isoCode", type="string", length=20, nullable=true)
     */
    private $isocode;

    /**
     * @var string
     *
     * @ORM\Column(name="mailType", type="string", length=200, nullable=true)
     */
    private $mailtype;

    /**
     * @var string
     *
     * @ORM\Column(name="shopName", type="string", length=255, nullable=true)
     */
    private $shopname;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $total;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=20, nullable=true)
     */
    private $currency;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="purchaseDate", type="datetime", nullable=false)
     */
    private $purchasedate = '0000-00-00 00:00:00';

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    private $status = 'PENDING';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sended_date", type="datetime", nullable=false)
     */
    private $sendedDate = '0000-00-00 00:00:00';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    private $modified;

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
     * Set shopId.
     *
     * @param int $shopId
     *
     * @return Transactionalqueuemails
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;

        return $this;
    }

    /**
     * Get shopId.
     *
     * @return int
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * Set shopshistoryId.
     *
     * @param int $shopshistoryId
     *
     * @return Transactionalqueuemails
     */
    public function setShopshistoryId($shopshistoryId)
    {
        $this->shopshistoryId = $shopshistoryId;

        return $this;
    }

    /**
     * Get shopshistoryId.
     *
     * @return int
     */
    public function getShopshistoryId()
    {
        return $this->shopshistoryId;
    }

    /**
     * Set cashbacktransactionId.
     *
     * @param string $cashbacktransactionId
     *
     * @return Transactionalqueuemails
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
     * Set idclient.
     *
     * @param int $idclient
     *
     * @return Transactionalqueuemails
     */
    public function setIdclient($idclient)
    {
        $this->idclient = $idclient;

        return $this;
    }

    /**
     * Get idclient.
     *
     * @return int
     */
    public function getIdclient()
    {
        return $this->idclient;
    }

    /**
     * Set isocode.
     *
     * @param string $isocode
     *
     * @return Transactionalqueuemails
     */
    public function setIsocode($isocode)
    {
        $this->isocode = $isocode;

        return $this;
    }

    /**
     * Get isocode.
     *
     * @return string
     */
    public function getIsocode()
    {
        return $this->isocode;
    }

    /**
     * Set mailtype.
     *
     * @param string $mailtype
     *
     * @return Transactionalqueuemails
     */
    public function setMailtype($mailtype)
    {
        $this->mailtype = $mailtype;

        return $this;
    }

    /**
     * Get mailtype.
     *
     * @return string
     */
    public function getMailtype()
    {
        return $this->mailtype;
    }

    /**
     * Set shopname.
     *
     * @param string $shopname
     *
     * @return Transactionalqueuemails
     */
    public function setShopname($shopname)
    {
        $this->shopname = $shopname;

        return $this;
    }

    /**
     * Get shopname.
     *
     * @return string
     */
    public function getShopname()
    {
        return $this->shopname;
    }

    /**
     * Set amount.
     *
     * @param string $amount
     *
     * @return Transactionalqueuemails
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
     * @return Transactionalqueuemails
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
     * @return Transactionalqueuemails
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
     * Set purchasedate.
     *
     * @param \DateTime $purchasedate
     *
     * @return Transactionalqueuemails
     */
    public function setPurchasedate($purchasedate)
    {
        $this->purchasedate = $purchasedate;

        return $this;
    }

    /**
     * Get purchasedate.
     *
     * @return \DateTime
     */
    public function getPurchasedate()
    {
        return $this->purchasedate;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return Transactionalqueuemails
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
     * @return Transactionalqueuemails
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
     * @return Transactionalqueuemails
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
     * @return Transactionalqueuemails
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
}
