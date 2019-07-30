<?php

namespace iFlair\LetsBonusMigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Affiliatetransactions.
 *
 * @ORM\Table(name="affiliatetransactions", indexes={@ORM\Index(name="n1_transactions", columns={"network_id"}), @ORM\Index(name="n2_transactions", columns={"shopshistory_id"}), @ORM\Index(name="n3_transactions", columns={"param0"}), @ORM\Index(name="n4_transactions", columns={"param1"}), @ORM\Index(name="n5_transactions", columns={"param2"}), @ORM\Index(name="transactionid", columns={"transactionid"}), @ORM\Index(name="reference_id", columns={"reference_id"}), @ORM\Index(name="status", columns={"status"}), @ORM\Index(name="program_id", columns={"program_id"}), @ORM\Index(name="processed", columns={"processed"})})
 * @ORM\Entity
 */
class Affiliatetransactions
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
     * @var string
     *
     * @ORM\Column(name="transactionid", type="string", length=255, nullable=false)
     */
    private $transactionid;

    /**
     * @var string
     *
     * @ORM\Column(name="reference_id", type="string", length=255, nullable=false)
     */
    private $referenceId;

    /**
     * @var int
     *
     * @ORM\Column(name="network_id", type="integer", nullable=false)
     */
    private $networkId;

    /**
     * @var int
     *
     * @ORM\Column(name="shopshistory_id", type="integer", nullable=false)
     */
    private $shopshistoryId;

    /**
     * @var string
     *
     * @ORM\Column(name="commission", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $commission;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=255, nullable=true)
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
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
     * @ORM\Column(name="trackingDate", type="string", length=255, nullable=true)
     */
    private $trackingdate;

    /**
     * @var string
     *
     * @ORM\Column(name="modifiedDate", type="string", length=255, nullable=true)
     */
    private $modifieddate;

    /**
     * @var string
     *
     * @ORM\Column(name="clickDate", type="string", length=255, nullable=true)
     */
    private $clickdate;

    /**
     * @var string
     *
     * @ORM\Column(name="clickId", type="string", length=255, nullable=true)
     */
    private $clickid;

    /**
     * @var string
     *
     * @ORM\Column(name="clickInId", type="string", length=255, nullable=true)
     */
    private $clickinid;

    /**
     * @var string
     *
     * @ORM\Column(name="program_id", type="string", length=255, nullable=true)
     */
    private $programId;

    /**
     * @var string
     *
     * @ORM\Column(name="program_name", type="string", length=255, nullable=true)
     */
    private $programName;

    /**
     * @var int
     *
     * @ORM\Column(name="param0", type="integer", nullable=true)
     */
    private $param0;

    /**
     * @var int
     *
     * @ORM\Column(name="param1", type="integer", nullable=true)
     */
    private $param1;

    /**
     * @var int
     *
     * @ORM\Column(name="param2", type="integer", nullable=true)
     */
    private $param2;

    /**
     * @var string
     *
     * @ORM\Column(name="orderNumber", type="string", length=255, nullable=true)
     */
    private $ordernumber;

    /**
     * @var string
     *
     * @ORM\Column(name="orderValue", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $ordervalue;

    /**
     * @var string
     *
     * @ORM\Column(name="trackingUrl", type="string", length=255, nullable=true)
     */
    private $trackingurl;

    /**
     * @var string
     *
     * @ORM\Column(name="productName", type="string", length=255, nullable=true)
     */
    private $productname;

    /**
     * @var string
     *
     * @ORM\Column(name="daysToAutoApprove", type="string", length=255, nullable=true)
     */
    private $daystoautoapprove;

    /**
     * @var string
     *
     * @ORM\Column(name="leadnumber", type="string", length=255, nullable=true)
     */
    private $leadnumber;

    /**
     * @var string
     *
     * @ORM\Column(name="processed", type="string", length=255, nullable=true)
     */
    private $processed = 'PENDING';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="process_date", type="datetime", nullable=true)
     */
    private $processDate;

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
     * Set transactionid.
     *
     * @param string $transactionid
     *
     * @return Affiliatetransactions
     */
    public function setTransactionid($transactionid)
    {
        $this->transactionid = $transactionid;

        return $this;
    }

    /**
     * Get transactionid.
     *
     * @return string
     */
    public function getTransactionid()
    {
        return $this->transactionid;
    }

    /**
     * Set referenceId.
     *
     * @param string $referenceId
     *
     * @return Affiliatetransactions
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
     * Set networkId.
     *
     * @param int $networkId
     *
     * @return Affiliatetransactions
     */
    public function setNetworkId($networkId)
    {
        $this->networkId = $networkId;

        return $this;
    }

    /**
     * Get networkId.
     *
     * @return int
     */
    public function getNetworkId()
    {
        return $this->networkId;
    }

    /**
     * Set shopshistoryId.
     *
     * @param int $shopshistoryId
     *
     * @return Affiliatetransactions
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
     * Set commission.
     *
     * @param string $commission
     *
     * @return Affiliatetransactions
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
     * Set amount.
     *
     * @param string $amount
     *
     * @return Affiliatetransactions
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
     * Set currency.
     *
     * @param string $currency
     *
     * @return Affiliatetransactions
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
     * Set status.
     *
     * @param string $status
     *
     * @return Affiliatetransactions
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
     * @param string $statusName
     *
     * @return Affiliatetransactions
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
     * @param string $statusState
     *
     * @return Affiliatetransactions
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
     * @param string $statusMessage
     *
     * @return Affiliatetransactions
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
     * Set trackingdate.
     *
     * @param string $trackingdate
     *
     * @return Affiliatetransactions
     */
    public function setTrackingdate($trackingdate)
    {
        $this->trackingdate = $trackingdate;

        return $this;
    }

    /**
     * Get trackingdate.
     *
     * @return string
     */
    public function getTrackingdate()
    {
        return $this->trackingdate;
    }

    /**
     * Set modifieddate.
     *
     * @param string $modifieddate
     *
     * @return Affiliatetransactions
     */
    public function setModifieddate($modifieddate)
    {
        $this->modifieddate = $modifieddate;

        return $this;
    }

    /**
     * Get modifieddate.
     *
     * @return string
     */
    public function getModifieddate()
    {
        return $this->modifieddate;
    }

    /**
     * Set clickdate.
     *
     * @param string $clickdate
     *
     * @return Affiliatetransactions
     */
    public function setClickdate($clickdate)
    {
        $this->clickdate = $clickdate;

        return $this;
    }

    /**
     * Get clickdate.
     *
     * @return string
     */
    public function getClickdate()
    {
        return $this->clickdate;
    }

    /**
     * Set clickid.
     *
     * @param string $clickid
     *
     * @return Affiliatetransactions
     */
    public function setClickid($clickid)
    {
        $this->clickid = $clickid;

        return $this;
    }

    /**
     * Get clickid.
     *
     * @return string
     */
    public function getClickid()
    {
        return $this->clickid;
    }

    /**
     * Set clickinid.
     *
     * @param string $clickinid
     *
     * @return Affiliatetransactions
     */
    public function setClickinid($clickinid)
    {
        $this->clickinid = $clickinid;

        return $this;
    }

    /**
     * Get clickinid.
     *
     * @return string
     */
    public function getClickinid()
    {
        return $this->clickinid;
    }

    /**
     * Set programId.
     *
     * @param string $programId
     *
     * @return Affiliatetransactions
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
     * @return Affiliatetransactions
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
     * Set param0.
     *
     * @param int $param0
     *
     * @return Affiliatetransactions
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
     * @return Affiliatetransactions
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
     * @return Affiliatetransactions
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
     * Set ordernumber.
     *
     * @param string $ordernumber
     *
     * @return Affiliatetransactions
     */
    public function setOrdernumber($ordernumber)
    {
        $this->ordernumber = $ordernumber;

        return $this;
    }

    /**
     * Get ordernumber.
     *
     * @return string
     */
    public function getOrdernumber()
    {
        return $this->ordernumber;
    }

    /**
     * Set ordervalue.
     *
     * @param string $ordervalue
     *
     * @return Affiliatetransactions
     */
    public function setOrdervalue($ordervalue)
    {
        $this->ordervalue = $ordervalue;

        return $this;
    }

    /**
     * Get ordervalue.
     *
     * @return string
     */
    public function getOrdervalue()
    {
        return $this->ordervalue;
    }

    /**
     * Set trackingurl.
     *
     * @param string $trackingurl
     *
     * @return Affiliatetransactions
     */
    public function setTrackingurl($trackingurl)
    {
        $this->trackingurl = $trackingurl;

        return $this;
    }

    /**
     * Get trackingurl.
     *
     * @return string
     */
    public function getTrackingurl()
    {
        return $this->trackingurl;
    }

    /**
     * Set productname.
     *
     * @param string $productname
     *
     * @return Affiliatetransactions
     */
    public function setProductname($productname)
    {
        $this->productname = $productname;

        return $this;
    }

    /**
     * Get productname.
     *
     * @return string
     */
    public function getProductname()
    {
        return $this->productname;
    }

    /**
     * Set daystoautoapprove.
     *
     * @param string $daystoautoapprove
     *
     * @return Affiliatetransactions
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
     * Set leadnumber.
     *
     * @param string $leadnumber
     *
     * @return Affiliatetransactions
     */
    public function setLeadnumber($leadnumber)
    {
        $this->leadnumber = $leadnumber;

        return $this;
    }

    /**
     * Get leadnumber.
     *
     * @return string
     */
    public function getLeadnumber()
    {
        return $this->leadnumber;
    }

    /**
     * Set processed.
     *
     * @param string $processed
     *
     * @return Affiliatetransactions
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
     * Set processDate.
     *
     * @param \DateTime $processDate
     *
     * @return Affiliatetransactions
     */
    public function setProcessDate($processDate)
    {
        $this->processDate = $processDate;

        return $this;
    }

    /**
     * Get processDate.
     *
     * @return \DateTime
     */
    public function getProcessDate()
    {
        return $this->processDate;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Affiliatetransactions
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
     * @return Affiliatetransactions
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
