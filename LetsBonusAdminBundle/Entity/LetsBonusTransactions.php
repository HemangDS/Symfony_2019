<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LetsBonusTransactions.
 *
 * @ORM\Table(name="lb_transactions")
 * @ORM\Entity
 */
class LetsBonusTransactions
{
    const PROCESSED_TYPE_PENDING = 'PENDING';
    const PROCESSED_TYPE_PROCESSED = 'PROCESSED';
    const STATUS_TYPE_APPROVED = 'approved';
    const STATUS_TYPE_CANCELLED = 'cancelled';
    const STATUS_TYPE_PENDING = 'pending';
    const STATUS_TYPE_PAID = 'paid';
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
     * @ORM\Column(name="transaction_id", type="string", length=255)
     */
    private $transactionId;

    /**
     * @var string
     *
     * @ORM\Column(name="reference_id", type="string", length=255, nullable=true)
     */
    private $referenceId;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="commission", type="decimal", precision=10, scale=2)
     */
    private $commission;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="status_name", type="string", length=255, nullable=true)
     */
    private $statusName;

    /**
     * @var string
     *
     * @ORM\Column(name="status_state", type="string", length=255, nullable=true)
     */
    private $statusState;

    /**
     * @var string
     *
     * @ORM\Column(name="status_message", type="string", length=255, nullable=true)
     */
    private $statusMessage;

    /**
     * @var string
     *
     * @ORM\Column(name="lead_number", type="string", length=255, nullable=true)
     */
    private $leadNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="processed", type="string", length=255)
     */
    private $processed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="processed_date", type="datetime")
     */
    private $processedDate;

    /**
     * @var string
     *
     * @ORM\Column(name="daystoautoapprove", type="string", length=255, nullable=true)
     */
    private $daystoautoapprove;

    /**
     * @var int
     *
     * @ORM\Column(name="param_0", type="integer", nullable=true)
     */
    private $param0;

    /**
     * @var int
     *
     * @ORM\Column(name="param_1", type="integer", nullable=true)
     */
    private $param1;

    /**
     * @var int
     *
     * @ORM\Column(name="param_2", type="integer", nullable=true)
     */
    private $param2;

    /**
     * @ORM\ManyToOne(targetEntity="shopHistory", inversedBy="letsbonusTransactions")
     */
    private $shopHistory;

    /**
     * @ORM\ManyToOne(targetEntity="Network", inversedBy="letsbonusTransactions")
     */
    private $network;

    /**
     * @ORM\ManyToOne(targetEntity="Currency", inversedBy="letsbonusTransactions")
     */
    private $currency;

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
     * @var string
     *
     * @ORM\Column(name="click_date", type="string", length=255, nullable=true)
     */
    private $clickDate;

    /**
     * @var string
     *
     * @ORM\Column(name="click_id", type="string", length=255, nullable=true)
     */
    private $clickId;

    /**
     * @var string
     *
     * @ORM\Column(name="click_in_id", type="string", length=255, nullable=true)
     */
    private $clickInId;

    /**
     * @var string
     *
     * @ORM\Column(name="tracking_date", type="string", length=255)
     */
    private $trackingDate;

    /**
     * @var string
     *
     * @ORM\Column(name="modified_date", type="string", length=255, nullable=true)
     */
    private $modifiedDate;

    /**
     * @var string
     *
     * @ORM\Column(name="tracking_url", type="string", length=255, nullable=true)
     */
    private $trackingUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="product_name", type="string", length=255, nullable=true)
     */
    private $productName;

    /**
     * @var string
     *
     * @ORM\Column(name="order_number", type="string", length=255, nullable=true)
     */
    private $orderNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="order_value", type="integer", nullable=true)
     */
    private $orderValue;

    /**
     * @var string
     *
     * @ORM\Column(name="program_id", type="string", length=255, nullable=true)
     */
    private $programId;

    /**
     * @var string
     *
     * @ORM\Column(name="program_name", type="string", length=255)
     */
    private $programName;

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
     * Set id.
     *
     * @param int $id
     *
     * @return LetsBonusTransactions
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set transactionId.
     *
     * @param string $transactionId
     *
     * @return LetsBonusTransactions
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * Get transactionId.
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * Set referenceId.
     *
     * @param string $referenceId
     *
     * @return LetsBonusTransactions
     */
    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;

        return $this;
    }

    /**
     * Get referenceId.
     *
     * @return string
     */
    public function getReferenceId()
    {
        return $this->referenceId;
    }

    /**
     * Set amount.
     *
     * @param string $amount
     *
     * @return LetsBonusTransactions
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
     * Set commission.
     *
     * @param string $commission
     *
     * @return LetsBonusTransactions
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * Get commission.
     *
     * @return string
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * Set status.
     *
     * @param string status
     *
     * @return LetsBonusTransactions
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
     * Set statusName.
     *
     * @param string statusName
     *
     * @return LetsBonusTransactions
     */
    public function setStatusName($statusName)
    {
        $this->statusName = $statusName;

        return $this;
    }

    /**
     * Get statusName.
     *
     * @return string
     */
    public function getStatusName()
    {
        return $this->statusName;
    }

    /**
     * Set statusState.
     *
     * @param string statusState
     *
     * @return LetsBonusTransactions
     */
    public function setStatusState($statusState)
    {
        $this->statusState = $statusState;

        return $this;
    }

    /**
     * Get statusState.
     *
     * @return string
     */
    public function getStatusState()
    {
        return $this->statusState;
    }

    /**
     * Set statusMessage.
     *
     * @param string statusMessage
     *
     * @return LetsBonusTransactions
     */
    public function setStatusMessage($statusMessage)
    {
        $this->statusMessage = $statusMessage;

        return $this;
    }

    /**
     * Get statusMessage.
     *
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * Set leadNumber.
     *
     * @param string leadNumber
     *
     * @return LetsBonusTransactions
     */
    public function setLeadNumber($leadNumber)
    {
        $this->leadNumber = $leadNumber;

        return $this;
    }

    /**
     * Get leadNumber.
     *
     * @return string
     */
    public function getLeadNumber()
    {
        return $this->leadNumber;
    }

    /**
     * Set processed.
     *
     * @param string processed
     *
     * @return LetsBonusTransactions
     */
    public function setProcessed($processed)
    {
        $this->processed = $processed;

        return $this;
    }

    /**
     * Get processed.
     *
     * @return string
     */
    public function getProcessed()
    {
        return $this->processed;
    }

    /**
     * Set processedDate.
     *
     * @param \DateTime $processedDate
     *
     * @return LetsBonusTransactions
     */
    public function setProcessedDate($processedDate)
    {
        $this->processedDate = $processedDate;

        return $this;
    }

    /**
     * Get processedDate.
     *
     * @return \DateTime
     */
    public function getProcessedDate()
    {
        return $this->processedDate;
    }

    /**
     * Set daystoautoapprove.
     *
     * @param string daystoautoapprove
     *
     * @return LetsBonusTransactions
     */
    public function setDaystoautoapprove($daystoautoapprove)
    {
        $this->daystoautoapprove = $daystoautoapprove;

        return $this;
    }

    /**
     * Get daystoautoapprove.
     *
     * @return string
     */
    public function getDaystoautoapprove()
    {
        return $this->daystoautoapprove;
    }

    /**
     * Set param0.
     *
     * @param int $param0
     *
     * @return LetsBonusTransactions
     */
    public function setParam0($param0)
    {
        $this->param0 = $param0;

        return $this;
    }

    /**
     * Get param0.
     *
     * @return int
     */
    public function getParam0()
    {
        return $this->param0;
    }

    /**
     * Set param1.
     *
     * @param int $param1
     *
     * @return LetsBonusTransactions
     */
    public function setParam1($param1)
    {
        $this->param1 = $param1;

        return $this;
    }

    /**
     * Get param1.
     *
     * @return int
     */
    public function getParam1()
    {
        return $this->param1;
    }

    /**
     * Set param2.
     *
     * @param int $param2
     *
     * @return LetsBonusTransactions
     */
    public function setParam2($param2)
    {
        $this->param2 = $param2;

        return $this;
    }

    /**
     * Get param2.
     *
     * @return int
     */
    public function getParam2()
    {
        return $this->param2;
    }

    /**
     * Set network.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Network $network
     *
     * @return LetsBonusTransactions
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
     * Set currency.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Currency $currency
     *
     * @return LetsBonusTransactions
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
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return CmsAboutus
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
     * @return CmsAboutus
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
     * Set clickDate.
     *
     * @param string clickDate
     *
     * @return LetsBonusTransactions
     */
    public function setClickDate($clickDate)
    {
        $this->clickDate = $clickDate;

        return $this;
    }

    /**
     * Get clickDate.
     *
     * @return string
     */
    public function getClickDate()
    {
        return $this->clickDate;
    }

    /**
     * Set clickId.
     *
     * @param string clickId
     *
     * @return LetsBonusTransactions
     */
    public function setClickId($clickId)
    {
        $this->clickId = $clickId;

        return $this;
    }

    /**
     * Get clickId.
     *
     * @return string
     */
    public function getClickId()
    {
        return $this->clickId;
    }

    /**
     * Set clickInId.
     *
     * @param string clickInId
     *
     * @return LetsBonusTransactions
     */
    public function setClickInId($clickInId)
    {
        $this->clickInId = $clickInId;

        return $this;
    }

    /**
     * Get clickInId.
     *
     * @return string
     */
    public function getClickInId()
    {
        return $this->clickInId;
    }

    /**
     * Set trackingDate.
     *
     * @param string trackingDate
     *
     * @return LetsBonusTransactions
     */
    public function setTrackingDate($trackingDate)
    {
        $this->trackingDate = $trackingDate;

        return $this;
    }

    /**
     * Get trackingDate.
     *
     * @return string
     */
    public function getTrackingDate()
    {
        return $this->trackingDate;
    }

    /**
     * Set trackingUrl.
     *
     * @param string trackingUrl
     *
     * @return LetsBonusTransactions
     */
    public function setTrackingUrl($trackingUrl)
    {
        $this->trackingUrl = $trackingUrl;

        return $this;
    }

    /**
     * Get trackingUrl.
     *
     * @return string
     */
    public function getTrackingUrl()
    {
        return $this->trackingUrl;
    }

    /**
     * Set orderNumber.
     *
     * @param string orderNumber
     *
     * @return LetsBonusTransactions
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Get orderNumber.
     *
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Set orderValue.
     *
     * @param string orderValue
     *
     * @return LetsBonusTransactions
     */
    public function setOrderValue($orderValue)
    {
        $this->orderValue = $orderValue;

        return $this;
    }

    /**
     * Get orderValue.
     *
     * @return string
     */
    public function getOrderValue()
    {
        return $this->orderValue;
    }

    /**
     * Set programId.
     *
     * @param string $programId
     *
     * @return LetsBonusTransactions
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
     * Set programName.
     *
     * @param string $programName
     *
     * @return LetsBonusTransactions
     */
    public function setProgramName($programName)
    {
        $this->programName = $programName;

        return $this;
    }

    /**
     * Get programName.
     *
     * @return string
     */
    public function getProgramName()
    {
        return $this->programName;
    }

    /**
     * Set shopHistory.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\shopHistory $shopHistory
     *
     * @return LetsBonusTransactions
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

    /**
     * Set modifiedDate.
     *
     * @param string $modifiedDate
     *
     * @return LetsBonusTransactions
     */
    public function setModifiedDate($modifiedDate)
    {
        $this->modifiedDate = $modifiedDate;

        return $this;
    }

    /**
     * Get modifiedDate.
     *
     * @return string
     */
    public function getModifiedDate()
    {
        return $this->modifiedDate;
    }

    /**
     * Set productName.
     *
     * @param string $productName
     *
     * @return LetsBonusTransactions
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * Get productName.
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }
}
