<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Variation.
 *
 * @ORM\Table(name="lb_variation")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\VariationRepository")
 */
class Variation
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
     * @ORM\Column(name="shopVariationId", type="integer", nullable=true)
     */
    private $shopVariationId;

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=255)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var shopHistory
     * @ORM\ManyToOne(targetEntity="shopHistory" , inversedBy="variation")
     * @ORM\JoinColumn(name="shop_history_id", referencedColumnName="id", nullable=FALSE)
     */
    private $shopHistory;

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

    public function __toString()
    {
        return strval($this->id);
    }

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
     * Set shopVariationId.
     *
     * @param string $shopVariationId
     *
     * @return Variation
     */
    public function setShopVariationId($shopVariationId)
    {
        $this->shopVariationId = $shopVariationId;

        return $this;
    }

    /**
     * Get shopVariationId.
     *
     * @return int
     */
    public function getShopVariationId()
    {
        return $this->shopVariationId;
    }

    /**
     * Set number.
     *
     * @param string $number
     *
     * @return Variation
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number.
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Variation
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set date.
     *
     * @param \DateTime $startDate
     *
     * @return Variation
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    public function setShopHistory(shopHistory $shopHistory = null)
    {
        $this->shopHistory = $shopHistory;

        return $this;
    }

    public function getShopHistory()
    {
        return $this->shopHistory;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return shopHistory
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
     * @return shopHistory
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
