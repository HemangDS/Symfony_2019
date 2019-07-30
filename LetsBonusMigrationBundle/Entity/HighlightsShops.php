<?php

namespace iFlair\LetsBonusMigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HighlightsShops.
 *
 * @ORM\Table(name="highlights_shops", indexes={@ORM\Index(name="highlight_id", columns={"highlight_id"}), @ORM\Index(name="shop_id", columns={"shop_id"}), @ORM\Index(name="order", columns={"order"})})
 * @ORM\Entity
 */
class HighlightsShops
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
     * @ORM\Column(name="highlight_id", type="integer", nullable=true)
     */
    private $highlightId;

    /**
     * @var int
     *
     * @ORM\Column(name="shop_id", type="integer", nullable=true)
     */
    private $shopId;

    /**
     * @var int
     *
     * @ORM\Column(name="order", type="integer", nullable=true)
     */
    private $order = '10000';

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
     * Set highlightId.
     *
     * @param int $highlightId
     *
     * @return HighlightsShops
     */
    public function setHighlightId($highlightId)
    {
        $this->highlightId = $highlightId;

        return $this;
    }

    /**
     * Get highlightId.
     *
     * @return int
     */
    public function getHighlightId()
    {
        return $this->highlightId;
    }

    /**
     * Set shopId.
     *
     * @param int $shopId
     *
     * @return HighlightsShops
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
     * Set order.
     *
     * @param int $order
     *
     * @return HighlightsShops
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order.
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return HighlightsShops
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
     * @return HighlightsShops
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
