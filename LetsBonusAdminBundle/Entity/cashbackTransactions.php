<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * cashbackTransactions.
 *
 * @ORM\Table(name="lb_cashback_transactions")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\cashbackTransactionsRepository")
 */
class cashbackTransactions
{
    const TRANSACTION_TYPE_MANUAL = 'manual';
    const TRANSACTION_TYPE_PROMO = 'promotion';
    const TRANSACTION_TYPE_ADDED = 'added';
    const TRANSACTION_TYPE_WITHDRAWAL = 'withdrawal';
    const TRANSACTION_TYPE_VOUCHER = 'voucher';
    const TRANSACTION_TYPE_DRAFT = 'draft';
    const STATUS_TYPE_CONFIRMED = 'confirmed';
    const STATUS_TYPE_PENDING = 'pending';
    const STATUS_TYPE_DENIED = 'denied';
    const STATUS_TYPE_PAYED = 'payed';
    const STATUS_TYPE_CANCELLED = 'cancelled';
    const STATUS_TYPE_APPROVED = 'approved';
    const NETWORK_STATUS_APPROVED = 'approved';
    const NETWORK_STATUS_CANCELLED = 'cancelled';
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
     * @ORM\ManyToOne(targetEntity="Shop", inversedBy="cashbackTransactions")
     * @ORM\JoinColumn(name="shop_id", referencedColumnName="id")
     */
    private $shopId;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="shopHistory", inversedBy="cashbackTransactions")
     * @ORM\JoinColumn(name="shop_history_id", referencedColumnName="id")
     */
    private $shopHistory;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
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
     * @ORM\ManyToOne(targetEntity="Network", inversedBy="cashbackTransactions")
     * @ORM\JoinColumn(name="network_id", referencedColumnName="id")
     */
    private $networkId;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2)
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
     * @var Currency
     * @ORM\ManyToOne(targetEntity="Currency", inversedBy="cashbackTransactions")
     * @ORM\JoinColumn(name="currency", referencedColumnName="id")
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=50)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50)
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
     * @ORM\Column(name="affiliate_canceldate", type="datetime", nullable=true)
     */
    private $affiliateCanceldate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="aproval_date", type="datetime", nullable=true)
     */
    private $aprovalDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

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
     * @ORM\ManyToOne(targetEntity="Companies", inversedBy="cashbackTransactions")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     */
    private $companyId;

    /**
     * @var string
     *
     * @ORM\Column(name="cashbacktransactions_childs", type="text", nullable=true)
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
     * @ORM\Column(name="comments", type="text", nullable=true)
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
     * @ORM\ManyToOne(targetEntity="cashbackSettings", inversedBy="cashbackTransactions")
     * @ORM\JoinColumn(name="cashbacksetting_id", referencedColumnName="id", nullable=true)
     */
    private $cashbacksettingId;

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
     * @ORM\Column(name="device_type", type="string", length=255, nullable=true)
     */
    private $deviceType;

    /**
     * @var int
     *
     * @ORM\Column(name="denied_mail_status", type="integer", nullable=true)
     */
    private $deniedMailStatus = 0;

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
     * Set id.
     *
     * @param int $id
     *
     * @return cashbackTransactions
     */
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
     * Set shopId.
     *
     * @param int $shopId
     *
     * @return cashbackTransactions
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
     * Set userId.
     *
     * @param int $userId
     *
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency.
     *
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set status.
     *
     * @param int $status
     *
     * @return cashbackTransactions
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
     * Set type.
     *
     * @param string $type
     *
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * @return cashbackTransactions
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
     * Set shopHistory.
     *
     * @param int $shopHistory
     *
     * @return cashbackTransactions
     */
    public function setShopHistory($shopHistory)
    {
        $this->shopHistory = $shopHistory;

        return $this;
    }

    /**
     * Get shopHistory.
     *
     * @return int
     */
    public function getShopHistory()
    {
        return $this->shopHistory;
    }

    /**
     * Set deniedMailStatus.
     *
     * @param int $deniedMailStatus
     *
     * @return cashbackTransactions
     */
    public function setDeniedMailStatus($deniedMailStatus)
    {
        $this->deniedMailStatus = $deniedMailStatus;

        return $this;
    }

    /**
     * Get deniedMailStatus.
     *
     * @return int
     */
    public function getDeniedMailStatus()
    {
        return $this->deniedMailStatus;
    }
}
