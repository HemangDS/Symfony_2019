<?php

namespace iFlair\LetsBonusMigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Shops.
 *
 * @ORM\Table(name="shops", indexes={@ORM\Index(name="n1_shops", columns={"network_id"}), @ORM\Index(name="n2_shops", columns={"company_id"}), @ORM\Index(name="status", columns={"status"}), @ORM\Index(name="start_date", columns={"start_date"}), @ORM\Index(name="end_date", columns={"end_date"})})
 * @ORM\Entity
 */
class Shops
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
     * @ORM\Column(name="network_id", type="integer", nullable=true)
     */
    private $networkId;

    /**
     * @var int
     *
     * @ORM\Column(name="company_id", type="integer", nullable=true)
     */
    private $companyId = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="text", length=65535, nullable=true)
     */
    private $keywords;

    /**
     * @var string
     *
     * @ORM\Column(name="img_logo", type="string", length=255, nullable=true)
     */
    private $imgLogo;

    /**
     * @var string
     *
     * @ORM\Column(name="img_logo2", type="string", length=255, nullable=true)
     */
    private $imgLogo2;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="url_afiliacion", type="string", length=255, nullable=true)
     */
    private $urlAfiliacion;

    /**
     * @var string
     *
     * @ORM\Column(name="lat", type="string", length=45, nullable=true)
     */
    private $lat;

    /**
     * @var string
     *
     * @ORM\Column(name="lng", type="string", length=45, nullable=true)
     */
    private $lng;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="postcode", type="string", length=15, nullable=true)
     */
    private $postcode;

    /**
     * @var string
     *
     * @ORM\Column(name="brand", type="string", length=255, nullable=true)
     */
    private $brand;

    /**
     * @var string
     *
     * @ORM\Column(name="program_id", type="string", length=255, nullable=true)
     */
    private $programId;

    /**
     * @var int
     *
     * @ORM\Column(name="daystoconfirm", type="integer", nullable=true)
     */
    private $daystoconfirm;

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
     * @var string
     *
     * @ORM\Column(name="internal_comments", type="text", length=65535, nullable=true)
     */
    private $internalComments;

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
     * Set networkId.
     *
     * @param int $networkId
     *
     * @return Shops
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
     * Set companyId.
     *
     * @param int $companyId
     *
     * @return Shops
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
     * Set keywords.
     *
     * @param string $keywords
     *
     * @return Shops
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords.
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set imgLogo.
     *
     * @param string $imgLogo
     *
     * @return Shops
     */
    public function setImgLogo($imgLogo)
    {
        $this->imgLogo = $imgLogo;

        return $this;
    }

    /**
     * Get imgLogo.
     *
     * @return string
     */
    public function getImgLogo()
    {
        return $this->imgLogo;
    }

    /**
     * Set imgLogo2.
     *
     * @param string $imgLogo2
     *
     * @return Shops
     */
    public function setImgLogo2($imgLogo2)
    {
        $this->imgLogo2 = $imgLogo2;

        return $this;
    }

    /**
     * Get imgLogo2.
     *
     * @return string
     */
    public function getImgLogo2()
    {
        return $this->imgLogo2;
    }

    /**
     * Set status.
     *
     * @param bool $status
     *
     * @return Shops
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set urlAfiliacion.
     *
     * @param string $urlAfiliacion
     *
     * @return Shops
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
     * Set lat.
     *
     * @param string $lat
     *
     * @return Shops
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat.
     *
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng.
     *
     * @param string $lng
     *
     * @return Shops
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng.
     *
     * @return string
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set address.
     *
     * @param string $address
     *
     * @return Shops
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set city.
     *
     * @param string $city
     *
     * @return Shops
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set postcode.
     *
     * @param string $postcode
     *
     * @return Shops
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * Get postcode.
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set brand.
     *
     * @param string $brand
     *
     * @return Shops
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand.
     *
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set programId.
     *
     * @param string $programId
     *
     * @return Shops
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
     * Set daystoconfirm.
     *
     * @param int $daystoconfirm
     *
     * @return Shops
     */
    public function setDaystoconfirm($daystoconfirm)
    {
        $this->daystoconfirm = $daystoconfirm;

        return $this;
    }

    /**
     * Get daystoconfirm.
     *
     * @return int
     */
    public function getDaystoconfirm()
    {
        return $this->daystoconfirm;
    }

    /**
     * Set startDate.
     *
     * @param \DateTime $startDate
     *
     * @return Shops
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
     * @return Shops
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
     * Set internalComments.
     *
     * @param string $internalComments
     *
     * @return Shops
     */
    public function setInternalComments($internalComments)
    {
        $this->internalComments = $internalComments;

        return $this;
    }

    /**
     * Get internalComments.
     *
     * @return string
     */
    public function getInternalComments()
    {
        return $this->internalComments;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Shops
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
     * @return Shops
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
