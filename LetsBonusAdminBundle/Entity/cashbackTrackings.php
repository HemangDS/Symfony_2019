<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * cashbackTrackings
 *
 * @ORM\Table(name="lb_cashback_trackings")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\cashbackTrackingsRepository")
 */
class cashbackTrackings
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="shopId", type="integer")
     */
    private $shopId;

    /**
     * @var integer
     *
     * @ORM\Column(name="userId", type="integer")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="programId", type="string", length=255)
     */
    private $programId;

    /**
     * @var string
     *
     * @ORM\Column(name="urlAffiliation", type="string", length=255)
     */
    private $urlAffiliation;

    /**
     * @var integer
     *
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="userAgent", type="text")
     */
    private $userAgent;

    /**
     * @var string
     *
     * @ORM\Column(name="redirectUrl", type="string", length=255)
     */
    private $redirectUrl;

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
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set shopId
     *
     * @param integer $shopId
     *
     * @return cashbackTrackings
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;

        return $this;
    }

    /**
     * Get shopId
     *
     * @return integer
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return cashbackTrackings
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set programId
     *
     * @param string $programId
     *
     * @return cashbackTrackings
     */
    public function setProgramId($programId)
    {
        $this->programId = $programId;

        return $this;
    }

    /**
     * Get programId
     *
     * @return string
     */
    public function getProgramId()
    {
        return $this->programId;
    }

    /**
     * Set urlAffiliation
     *
     * @param string $urlAffiliation
     *
     * @return cashbackTrackings
     */
    public function setUrlAffiliation($urlAffiliation)
    {
        $this->urlAffiliation = $urlAffiliation;

        return $this;
    }

    /**
     * Get urlAffiliation
     *
     * @return string
     */
    public function getUrlAffiliation()
    {
        return $this->urlAffiliation;
    }

    /**
     * Set ip
     *
     * @param integer $ip
     *
     * @return cashbackTrackings
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return integer
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set userAgent
     *
     * @param string $userAgent
     *
     * @return cashbackTrackings
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * Get userAgent
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set redirectUrl
     *
     * @param string $redirectUrl
     *
     * @return cashbackTrackings
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    /**
     * Get redirectUrl
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return cashbackTrackings
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     *
     * @return cashbackTrackings
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }
}

