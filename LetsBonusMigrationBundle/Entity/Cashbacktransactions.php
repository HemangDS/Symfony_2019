<?php

namespace iFlair\LetsBonusMigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cashbacktransactions.
 *
 * @ORM\Table(name="cashbacktransactions", indexes={@ORM\Index(name="n1_cashbacktransactions", columns={"shop_id"}), @ORM\Index(name="n2_cashbacktransactions", columns={"shopshistory_id"}), @ORM\Index(name="n3_cashbacktransactions", columns={"user_id"}), @ORM\Index(name="n4_cashbacktransactions", columns={"transaction_id"}), @ORM\Index(name="n5_cashbacktransactions", columns={"network_id"}), @ORM\Index(name="status", columns={"status"}), @ORM\Index(name="type", columns={"type"}), @ORM\Index(name="n6_cashbacktransactions", columns={"cashbacksetting_id"})})
 * @ORM\Entity
 */
class Cashbacktransactions
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
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_id", type="string", length=255, nullable=true)
     */
    private $transactionId;

    /**
     * @var int
     *
     * @ORM\Column(name="network_id", type="integer", nullable=true)
     */
    private $networkId;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="affiliate_amount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $affiliateAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="total_affiliate_amount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $totalAffiliateAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="letsbonus_pct", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $letsbonusPct;

    /**
     * @var string
     *
     * @ORM\Column(name="extra_amount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $extraAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="extra_pct", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $extraPct;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=10, nullable=true)
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
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="network_status", type="string", length=255, nullable=true)
     */
    private $networkStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="order_reference", type="string", length=255, nullable=true)
     */
    private $orderReference;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="affiliate_aproveddate", type="datetime", nullable=true)
     */
    private $affiliateAproveddate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="affiliate_canceldate", type="datetime", nullable=false)
     */
    private $affiliateCanceldate = '0000-00-00 00:00:00';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="aproval_date", type="datetime", nullable=false)
     */
    private $aprovalDate = '0000-00-00 00:00:00';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="user_name", type="string", length=255, nullable=true)
     */
    private $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="user_address", type="string", length=255, nullable=true)
     */
    private $userAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="user_dni", type="string", length=255, nullable=true)
     */
    private $userDni;

    /**
     * @var string
     *
     * @ORM\Column(name="user_phone", type="string", length=255, nullable=true)
     */
    private $userPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="user_bank_account_number", type="string", length=255, nullable=true)
     */
    private $userBankAccountNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="bic", type="string", length=255, nullable=true)
     */
    private $bic;

    /**
     * @var int
     *
     * @ORM\Column(name="company_id", type="integer", nullable=true)
     */
    private $companyId = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="cashbacktransactions_childs", type="text", length=65535, nullable=true)
     */
    private $cashbacktransactionsChilds;

    /**
     * @var int
     *
     * @ORM\Column(name="adminuser_id", type="integer", nullable=true)
     */
    private $adminuserId;

    /**
     * @var int
     *
     * @ORM\Column(name="manual_numdaystoapprove", type="integer", nullable=true)
     */
    private $manualNumdaystoapprove;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="text", length=65535, nullable=true)
     */
    private $comments;

    /**
     * @var int
     *
     * @ORM\Column(name="parent_transaction_id", type="integer", nullable=true)
     */
    private $parentTransactionId;

    /**
     * @var int
     *
     * @ORM\Column(name="cashbacksetting_id", type="integer", nullable=true)
     */
    private $cashbacksettingId = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="sepageneratedby_user_id", type="integer", nullable=true)
     */
    private $sepageneratedbyUserId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sepagenerated_date", type="datetime", nullable=true)
     */
    private $sepageneratedDate;

    /**
     * @var string
     *
     * @ORM\Column(name="device_type", type="string", length=20, nullable=true)
     */
    private $deviceType;

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
     * @return Cashbacktransactions
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
     * @return Cashbacktransactions
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
     * Set userId.
     *
     * @param int $userId
     *
     * @return Cashbacktransactions
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set transactionId.
     *
     * @param string $transactionId
     *
     * @return Cashbacktransactions
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
     * Set networkId.
     *
     * @param int $networkId
     *
     * @return Cashbacktransactions
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
     * Set amount.
     *
     * @param string $amount
     *
     * @return Cashbacktransactions
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
     * Set affiliateAmount.
     *
     * @param string $affiliateAmount
     *
     * @return Cashbacktransactions
     */
    public function setAffiliateAmount($affiliateAmount)
    {
        $this->affiliateAmount = $affiliateAmount;

        return $this;
    }

    /**
     * Get affiliateAmount.
     *
     * @return string
     */
    public function getAffiliateAmount()
    {
        return $this->affiliateAmount;
    }

    /**
     * Set totalAffiliateAmount.
     *
     * @param string $totalAffiliateAmount
     *
     * @return Cashbacktransactions
     */
    public function setTotalAffiliateAmount($totalAffiliateAmount)
    {
        $this->totalAffiliateAmount = $totalAffiliateAmount;

        return $this;
    }

    /**
     * Get totalAffiliateAmount.
     *
     * @return string
     */
    public function getTotalAffiliateAmount()
    {
        return $this->totalAffiliateAmount;
    }

    /**
     * Set letsbonusPct.
     *
     * @param string $letsbonusPct
     *
     * @return Cashbacktransactions
     */
    public function setLetsbonusPct($letsbonusPct)
    {
        $this->letsbonusPct = $letsbonusPct;

        return $this;
    }

    /**
     * Get letsbonusPct.
     *
     * @return string
     */
    public function getLetsbonusPct()
    {
        return $this->letsbonusPct;
    }

    /**
     * Set extraAmount.
     *
     * @param string $extraAmount
     *
     * @return Cashbacktransactions
     */
    public function setExtraAmount($extraAmount)
    {
        $this->extraAmount = $extraAmount;

        return $this;
    }

    /**
     * Get extraAmount.
     *
     * @return string
     */
    public function getExtraAmount()
    {
        return $this->extraAmount;
    }

    /**
     * Set extraPct.
     *
     * @param string $extraPct
     *
     * @return Cashbacktransactions
     */
    public function setExtraPct($extraPct)
    {
        $this->extraPct = $extraPct;

        return $this;
    }

    /**
     * Get extraPct.
     *
     * @return string
     */
    public function getExtraPct()
    {
        return $this->extraPct;
    }

    /**
     * Set currency.
     *
     * @param string $currency
     *
     * @return Cashbacktransactions
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
     * @return Cashbacktransactions
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
     * Set type.
     *
     * @param string $type
     *
     * @return Cashbacktransactions
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set networkStatus.
     *
     * @param string $networkStatus
     *
     * @return Cashbacktransactions
     */
    public function setNetworkStatus($networkStatus)
    {
        $this->networkStatus = $networkStatus;

        return $this;
    }

    /**
     * Get networkStatus.
     *
     * @return string
     */
    public function getNetworkStatus()
    {
        return $this->networkStatus;
    }

    /**
     * Set orderReference.
     *
     * @param string $orderReference
     *
     * @return Cashbacktransactions
     */
    public function setOrderReference($orderReference)
    {
        $this->orderReference = $orderReference;

        return $this;
    }

    /**
     * Get orderReference.
     *
     * @return string
     */
    public function getOrderReference()
    {
        return $this->orderReference;
    }

    /**
     * Set affiliateAproveddate.
     *
     * @param \DateTime $affiliateAproveddate
     *
     * @return Cashbacktransactions
     */
    public function setAffiliateAproveddate($affiliateAproveddate)
    {
        $this->affiliateAproveddate = $affiliateAproveddate;

        return $this;
    }

    /**
     * Get affiliateAproveddate.
     *
     * @return \DateTime
     */
    public function getAffiliateAproveddate()
    {
        return $this->affiliateAproveddate;
    }

    /**
     * Set affiliateCanceldate.
     *
     * @param \DateTime $affiliateCanceldate
     *
     * @return Cashbacktransactions
     */
    public function setAffiliateCanceldate($affiliateCanceldate)
    {
        $this->affiliateCanceldate = $affiliateCanceldate;

        return $this;
    }

    /**
     * Get affiliateCanceldate.
     *
     * @return \DateTime
     */
    public function getAffiliateCanceldate()
    {
        return $this->affiliateCanceldate;
    }

    /**
     * Set aprovalDate.
     *
     * @param \DateTime $aprovalDate
     *
     * @return Cashbacktransactions
     */
    public function setAprovalDate($aprovalDate)
    {
        $this->aprovalDate = $aprovalDate;

        return $this;
    }

    /**
     * Get aprovalDate.
     *
     * @return \DateTime
     */
    public function getAprovalDate()
    {
        return $this->aprovalDate;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return Cashbacktransactions
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set userName.
     *
     * @param string $userName
     *
     * @return Cashbacktransactions
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName.
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set userAddress.
     *
     * @param string $userAddress
     *
     * @return Cashbacktransactions
     */
    public function setUserAddress($userAddress)
    {
        $this->userAddress = $userAddress;

        return $this;
    }

    /**
     * Get userAddress.
     *
     * @return string
     */
    public function getUserAddress()
    {
        return $this->userAddress;
    }

    /**
     * Set userDni.
     *
     * @param string $userDni
     *
     * @return Cashbacktransactions
     */
    public function setUserDni($userDni)
    {
        $this->userDni = $userDni;

        return $this;
    }

    /**
     * Get userDni.
     *
     * @return string
     */
    public function getUserDni()
    {
        return $this->userDni;
    }

    /**
     * Set userPhone.
     *
     * @param string $userPhone
     *
     * @return Cashbacktransactions
     */
    public function setUserPhone($userPhone)
    {
        $this->userPhone = $userPhone;

        return $this;
    }

    /**
     * Get userPhone.
     *
     * @return string
     */
    public function getUserPhone()
    {
        return $this->userPhone;
    }

    /**
     * Set userBankAccountNumber.
     *
     * @param string $userBankAccountNumber
     *
     * @return Cashbacktransactions
     */
    public function setUserBankAccountNumber($userBankAccountNumber)
    {
        $this->userBankAccountNumber = $userBankAccountNumber;

        return $this;
    }

    /**
     * Get userBankAccountNumber.
     *
     * @return string
     */
    public function getUserBankAccountNumber()
    {
        return $this->userBankAccountNumber;
    }

    /**
     * Set bic.
     *
     * @param string $bic
     *
     * @return Cashbacktransactions
     */
    public function setBic($bic)
    {
        $this->bic = $bic;

        return $this;
    }

    /**
     * Get bic.
     *
     * @return string
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * Set companyId.
     *
     * @param int $companyId
     *
     * @return Cashbacktransactions
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;

        return $this;
    }

    /**
     * Get companyId.
     *
     * @return int
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Set cashbacktransactionsChilds.
     *
     * @param string $cashbacktransactionsChilds
     *
     * @return Cashbacktransactions
     */
    public function setCashbacktransactionsChilds($cashbacktransactionsChilds)
    {
        $this->cashbacktransactionsChilds = $cashbacktransactionsChilds;

        return $this;
    }

    /**
     * Get cashbacktransactionsChilds.
     *
     * @return string
     */
    public function getCashbacktransactionsChilds()
    {
        return $this->cashbacktransactionsChilds;
    }

    /**
     * Set adminuserId.
     *
     * @param int $adminuserId
     *
     * @return Cashbacktransactions
     */
    public function setAdminuserId($adminuserId)
    {
        $this->adminuserId = $adminuserId;

        return $this;
    }

    /**
     * Get adminuserId.
     *
     * @return int
     */
    public function getAdminuserId()
    {
        return $this->adminuserId;
    }

    /**
     * Set manualNumdaystoapprove.
     *
     * @param int $manualNumdaystoapprove
     *
     * @return Cashbacktransactions
     */
    public function setManualNumdaystoapprove($manualNumdaystoapprove)
    {
        $this->manualNumdaystoapprove = $manualNumdaystoapprove;

        return $this;
    }

    /**
     * Get manualNumdaystoapprove.
     *
     * @return int
     */
    public function getManualNumdaystoapprove()
    {
        return $this->manualNumdaystoapprove;
    }

    /**
     * Set comments.
     *
     * @param string $comments
     *
     * @return Cashbacktransactions
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments.
     *
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set parentTransactionId.
     *
     * @param int $parentTransactionId
     *
     * @return Cashbacktransactions
     */
    public function setParentTransactionId($parentTransactionId)
    {
        $this->parentTransactionId = $parentTransactionId;

        return $this;
    }

    /**
     * Get parentTransactionId.
     *
     * @return int
     */
    public function getParentTransactionId()
    {
        return $this->parentTransactionId;
    }

    /**
     * Set cashbacksettingId.
     *
     * @param int $cashbacksettingId
     *
     * @return Cashbacktransactions
     */
    public function setCashbacksettingId($cashbacksettingId)
    {
        $this->cashbacksettingId = $cashbacksettingId;

        return $this;
    }

    /**
     * Get cashbacksettingId.
     *
     * @return int
     */
    public function getCashbacksettingId()
    {
        return $this->cashbacksettingId;
    }

    /**
     * Set sepageneratedbyUserId.
     *
     * @param int $sepageneratedbyUserId
     *
     * @return Cashbacktransactions
     */
    public function setSepageneratedbyUserId($sepageneratedbyUserId)
    {
        $this->sepageneratedbyUserId = $sepageneratedbyUserId;

        return $this;
    }

    /**
     * Get sepageneratedbyUserId.
     *
     * @return int
     */
    public function getSepageneratedbyUserId()
    {
        return $this->sepageneratedbyUserId;
    }

    /**
     * Set sepageneratedDate.
     *
     * @param \DateTime $sepageneratedDate
     *
     * @return Cashbacktransactions
     */
    public function setSepageneratedDate($sepageneratedDate)
    {
        $this->sepageneratedDate = $sepageneratedDate;

        return $this;
    }

    /**
     * Get sepageneratedDate.
     *
     * @return \DateTime
     */
    public function getSepageneratedDate()
    {
        return $this->sepageneratedDate;
    }

    /**
     * Set deviceType.
     *
     * @param string $deviceType
     *
     * @return Cashbacktransactions
     */
    public function setDeviceType($deviceType)
    {
        $this->deviceType = $deviceType;

        return $this;
    }

    /**
     * Get deviceType.
     *
     * @return string
     */
    public function getDeviceType()
    {
        return $this->deviceType;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Cashbacktransactions
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
     * @return Cashbacktransactions
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
