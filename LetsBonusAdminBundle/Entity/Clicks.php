<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Clicks.
 *
 * @ORM\Table(name="lb_clicks")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\ClicksRepository")
 */
class Clicks
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
     *
     * @ORM\Column(name="shop_id", type="integer", nullable=true)
     */
    private $shopId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="shopshistory_id", type="integer", nullable=true)
     */
    private $shopshistoryId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="tab_type", type="string", length=255, nullable=true)
     */
    private $tabType;

    /**
     * @var string
     *
     * @ORM\Column(name="tab_id", type="string", length=255, nullable=true)
     */
    private $tabId;

    /**
     * @var int
     *
     * @ORM\Column(name="tab_position", type="integer", nullable=true)
     */
    private $tabPosition;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255, nullable=true)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="user_agent", type="string", length=500, nullable=true)
     */
    private $userAgent;

    /**
     * @var int
     *
     * @ORM\Column(name="company_id", type="integer", nullable=true)
     */
    private $companyId;

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

    public function __construct()
    {
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
     * @return Clicks
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set shopId.
     *
     * @param int $shopId
     *
     * @return Clicks
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
     * @return Clicks
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
     * Set shopshistoryId.
     *
     * @param int $shopshistoryId
     *
     * @return Clicks
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
     * Set type.
     *
     * @param string $type
     *
     * @return Clicks
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
     * Set tabType.
     *
     * @param string $tabType
     *
     * @return Clicks
     */
    public function setTabType($tabType)
    {
        $this->tabType = $tabType;

        return $this;
    }

    /**
     * Get tabType.
     *
     * @return string
     */
    public function getTabType()
    {
        return $this->tabType;
    }

    /**
     * Set tabId.
     *
     * @param string $tabId
     *
     * @return Clicks
     */
    public function setTabId($tabId)
    {
        $this->tabId = $tabId;

        return $this;
    }

    /**
     * Get tabId.
     *
     * @return string
     */
    public function getTabId()
    {
        return $this->tabId;
    }

    /**
     * Set tabPosition.
     *
     * @param int $tabPosition
     *
     * @return Clicks
     */
    public function setTabPosition($tabPosition)
    {
        $this->tabPosition = $tabPosition;

        return $this;
    }

    /**
     * Get tabPosition.
     *
     * @return int
     */
    public function getTabPosition()
    {
        return $this->tabPosition;
    }

    /**
     * Set ip.
     *
     * @param string $ip
     *
     * @return Clicks
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip.
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set userAgent.
     *
     * @param string $userAgent
     *
     * @return Clicks
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * Get userAgent.
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set companyId.
     *
     * @param int $companyId
     *
     * @return Clicks
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
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Clicks
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
     * @return Clicks
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
