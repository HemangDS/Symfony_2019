<?php

namespace iFlair\LetsBonusMigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cashbacktrackings.
 *
 * @ORM\Table(name="cashbacktrackings", indexes={@ORM\Index(name="n1_cashbacks_trackings", columns={"shop_id"}), @ORM\Index(name="n2_cashbacks_trackings", columns={"user_id"})})
 * @ORM\Entity
 */
class Cashbacktrackings
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
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="program_id", type="string", length=255, nullable=true)
     */
    private $programId;

    /**
     * @var string
     *
     * @ORM\Column(name="url_afiliacion", type="string", length=255, nullable=true)
     */
    private $urlAfiliacion;

    /**
     * @var int
     *
     * @ORM\Column(name="ip", type="integer", nullable=true)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="user_agent", type="text", length=65535, nullable=true)
     */
    private $userAgent;

    /**
     * @var string
     *
     * @ORM\Column(name="redirect_url", type="string", length=255, nullable=true)
     */
    private $redirectUrl;

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
     * @return Cashbacktrackings
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
     * @return Cashbacktrackings
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
     * Set programId.
     *
     * @param string $programId
     *
     * @return Cashbacktrackings
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
     * Set urlAfiliacion.
     *
     * @param string $urlAfiliacion
     *
     * @return Cashbacktrackings
     */
    public function setUrlAfiliacion($urlAfiliacion)
    {
        $this->urlAfiliacion = $urlAfiliacion;

        return $this;
    }

    /**
     * Get urlAfiliacion.
     *
     * @return string
     */
    public function getUrlAfiliacion()
    {
        return $this->urlAfiliacion;
    }

    /**
     * Set ip.
     *
     * @param int $ip
     *
     * @return Cashbacktrackings
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip.
     *
     * @return int
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
     * @return Cashbacktrackings
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
     * Set redirectUrl.
     *
     * @param string $redirectUrl
     *
     * @return Cashbacktrackings
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    /**
     * Get redirectUrl.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Cashbacktrackings
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
     * @return Cashbacktrackings
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
