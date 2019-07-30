<?php

namespace iFlair\LetsBonusMigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CashbacksettingsShops.
 *
 * @ORM\Table(name="cashbacksettings_shops")
 * @ORM\Entity
 */
class CashbacksettingsShops
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
     * @ORM\Column(name="cashbacksetting_id", type="integer", nullable=false)
     */
    private $cashbacksettingId;

    /**
     * @var int
     *
     * @ORM\Column(name="shop_id", type="integer", nullable=false)
     */
    private $shopId;

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
     * Set cashbacksettingId.
     *
     * @param int $cashbacksettingId
     *
     * @return CashbacksettingsShops
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
     * Set shopId.
     *
     * @param int $shopId
     *
     * @return CashbacksettingsShops
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
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return CashbacksettingsShops
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
     * @return CashbacksettingsShops
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
