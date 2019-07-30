<?php

namespace iFlair\LetsBonusMigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Shopshistories.
 *
 * @ORM\Table(name="shopshistories", indexes={@ORM\Index(name="n1_shopshistories", columns={"shop_id"}), @ORM\Index(name="n2_shopshistories", columns={"administrator_id"})})
 * @ORM\Entity
 */
class Shopshistories
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
     * @ORM\Column(name="administrator_id", type="integer", nullable=true)
     */
    private $administratorId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="intro", type="text", length=65535, nullable=true)
     */
    private $intro;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="conditions", type="text", length=65535, nullable=true)
     */
    private $conditions;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="string", length=20, nullable=true)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="percentage", type="string", length=20, nullable=true)
     */
    private $percentage;

    /**
     * @var string
     *
     * @ORM\Column(name="lbpercentage", type="decimal", precision=5, scale=2, nullable=true)
     */
    private $lbpercentage;

    /**
     * @var string
     *
     * @ORM\Column(name="url_afiliacion", type="string", length=255, nullable=true)
     */
    private $urlAfiliacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var int
     *
     * @ORM\Column(name="label_id", type="integer", nullable=true)
     */
    private $labelId;

    /**
     * @var string
     *
     * @ORM\Column(name="strikelabel", type="string", length=255, nullable=true)
     */
    private $strikelabel;

    /**
     * @var string
     *
     * @ORM\Column(name="deliveryinfo", type="string", length=255, nullable=true)
     */
    private $deliveryinfo;

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
     * @return Shopshistories
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
     * Set administratorId.
     *
     * @param int $administratorId
     *
     * @return Shopshistories
     */
    public function setAdministratorId($administratorId)
    {
        $this->administratorId = $administratorId;

        return $this;
    }

    /**
     * Get administratorId.
     *
     * @return int
     */
    public function getAdministratorId()
    {
        return $this->administratorId;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Shopshistories
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
     * Set url.
     *
     * @param string $url
     *
     * @return Shopshistories
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set intro.
     *
     * @param string $intro
     *
     * @return Shopshistories
     */
    public function setIntro($intro)
    {
        $this->intro = $intro;

        return $this;
    }

    /**
     * Get intro.
     *
     * @return string
     */
    public function getIntro()
    {
        return $this->intro;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Shopshistories
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set conditions.
     *
     * @param string $conditions
     *
     * @return Shopshistories
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;

        return $this;
    }

    /**
     * Get conditions.
     *
     * @return string
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Set price.
     *
     * @param string $price
     *
     * @return Shopshistories
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set percentage.
     *
     * @param string $percentage
     *
     * @return Shopshistories
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;

        return $this;
    }

    /**
     * Get percentage.
     *
     * @return string
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * Set lbpercentage.
     *
     * @param string $lbpercentage
     *
     * @return Shopshistories
     */
    public function setLbpercentage($lbpercentage)
    {
        $this->lbpercentage = $lbpercentage;

        return $this;
    }

    /**
     * Get lbpercentage.
     *
     * @return string
     */
    public function getLbpercentage()
    {
        return $this->lbpercentage;
    }

    /**
     * Set urlAfiliacion.
     *
     * @param string $urlAfiliacion
     *
     * @return Shopshistories
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
     * Set startDate.
     *
     * @param \DateTime $startDate
     *
     * @return Shopshistories
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate.
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate.
     *
     * @param \DateTime $endDate
     *
     * @return Shopshistories
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate.
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set labelId.
     *
     * @param int $labelId
     *
     * @return Shopshistories
     */
    public function setLabelId($labelId)
    {
        $this->labelId = $labelId;

        return $this;
    }

    /**
     * Get labelId.
     *
     * @return int
     */
    public function getLabelId()
    {
        return $this->labelId;
    }

    /**
     * Set strikelabel.
     *
     * @param string $strikelabel
     *
     * @return Shopshistories
     */
    public function setStrikelabel($strikelabel)
    {
        $this->strikelabel = $strikelabel;

        return $this;
    }

    /**
     * Get strikelabel.
     *
     * @return string
     */
    public function getStrikelabel()
    {
        return $this->strikelabel;
    }

    /**
     * Set deliveryinfo.
     *
     * @param string $deliveryinfo
     *
     * @return Shopshistories
     */
    public function setDeliveryinfo($deliveryinfo)
    {
        $this->deliveryinfo = $deliveryinfo;

        return $this;
    }

    /**
     * Get deliveryinfo.
     *
     * @return string
     */
    public function getDeliveryinfo()
    {
        return $this->deliveryinfo;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Shopshistories
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
     * @return Shopshistories
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
