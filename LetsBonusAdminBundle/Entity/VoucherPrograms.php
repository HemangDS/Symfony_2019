<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * VoucherPrograms.
 *
 * @ORM\Table(name="lb_voucher_programs")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\VoucherProgramsRepository")
 */
class VoucherPrograms
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
     * @ORM\Column(name="nprogram_id", type="integer")
     */
    private $nprogramId;

    /**
     * @var string
     *
     * @ORM\Column(name="program_name", type="string", length=255)
     */
    private $programName;

    /**
     * @var string
     *
     * @ORM\Column(name="logo_path", type="string", length=255, nullable=true)
     */
    private $logoPath;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Network", inversedBy="program")	
     * @ORM\JoinColumn(name="network_id", referencedColumnName="id")
     */
    private $network;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    private $image;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    private $banner;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    private $rightBlockImage;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    private $popUpImage;

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
     * @ORM\OneToMany(targetEntity="Voucher", mappedBy="program")
     */
    private $voucher;

    /**
     * @ORM\OneToMany(targetEntity="Shop", mappedBy="vprogram")
     */
    private $shop;

    /**
     * @ORM\OneToMany(targetEntity="iFlair\LetsBonusFrontBundle\Entity\Review", mappedBy="brandId")
     */
    private $review;

     /**
     * @ORM\OneToMany(targetEntity="offerSpecials", mappedBy="voucherProgramsId")
     */
    private $offerSpecials;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
        $this->shop = new ArrayCollection();
    }

    public function getShop()
    {
        return $this->shop;
    }

    public function __toString()
    {
        //return (string) $this->programName;
         return strval($this->programName);
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
     * Set programName.
     *
     * @param string $programName
     *
     * @return VoucherPrograms
     */
    public function setProgramName($programName)
    {
        $this->programName = $programName;

        return $this;
    }

    /**
     * Get programName.
     *
     * @return string
     */
    public function getProgramName()
    {
        return $this->programName;
    }

    /**
     * Set logoPath.
     *
     * @param string $logoPath
     *
     * @return VoucherPrograms
     */
    public function setLogoPath($logoPath)
    {
        $this->logoPath = $logoPath;

        return $this;
    }

    /**
     * Get logoPath.
     *
     * @return string
     */
    public function getLogoPath()
    {
        return $this->logoPath;
    }

    /**
     * Set image.
     *
     * @param string $image
     *
     * @return Slider
     */
    public function setImage(\Application\Sonata\MediaBundle\Entity\Media $image=null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return VoucherPrograms
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
     * @return VoucherPrograms
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
     * Set network.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Network $network
     *
     * @return VoucherPrograms
     */
    public function setNetwork(\iFlair\LetsBonusAdminBundle\Entity\Network $network = null)
    {
        $this->network = $network;

        return $this;
    }

    /**
     * Get network.
     *
     * @return \iFlair\LetsBonusAdminBundle\Entity\Network
     */
    public function getNetwork()
    {
        return $this->network;
    }

    /**
     * Add voucher.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Voucher $voucher
     *
     * @return VoucherPrograms
     */
    public function addVoucher(\iFlair\LetsBonusAdminBundle\Entity\Voucher $voucher)
    {
        $this->voucher[] = $voucher;

        return $this;
    }

    /**
     * Remove voucher.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Voucher $voucher
     */
    public function removeVoucher(\iFlair\LetsBonusAdminBundle\Entity\Voucher $voucher)
    {
        $this->voucher->removeElement($voucher);
    }

    /**
     * Get voucher.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoucher()
    {
        return $this->voucher;
    }

    /**
     * Set nprogramId.
     *
     * @param int $nprogramId
     *
     * @return VoucherPrograms
     */
    public function setNprogramId($nprogramId)
    {
        $this->nprogramId = $nprogramId;

        return $this;
    }

    /**
     * Get nprogramId.
     *
     * @return int
     */
    public function getNprogramId()
    {
        return $this->nprogramId;
    }

    /**
     * Set banner.
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $banner
     *
     * @return VoucherPrograms
     */
    public function setBanner(\Application\Sonata\MediaBundle\Entity\Media $banner = null)
    {
        $this->banner = $banner;

        return $this;
    }

    /**
     * Get banner.
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * Set rightBlockImage.
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $rightBlockImage
     *
     * @return VoucherPrograms
     */
    public function setRightBlockImage(\Application\Sonata\MediaBundle\Entity\Media $rightBlockImage = null)
    {
        $this->rightBlockImage = $rightBlockImage;

        return $this;
    }

    /**
     * Get rightBlockImage.
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getRightBlockImage()
    {
        return $this->rightBlockImage;
    }

    /**
     * Set popUpImage.
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $popUpImage
     *
     * @return VoucherPrograms
     */
    public function setPopUpImage(\Application\Sonata\MediaBundle\Entity\Media $popUpImage = null)
    {
        $this->popUpImage = $popUpImage;

        return $this;
    }

    /**
     * Get popUpImage.
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getPopUpImage()
    {
        return $this->popUpImage;
    }

    /**
     * Add shop.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Shop $shop
     *
     * @return VoucherPrograms
     */
    public function addShop(\iFlair\LetsBonusAdminBundle\Entity\Shop $shop)
    {
        $this->shop[] = $shop;

        return $this;
    }

    /**
     * Remove shop.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Shop $shop
     */
    public function removeShop(\iFlair\LetsBonusAdminBundle\Entity\Shop $shop)
    {
        $this->shop->removeElement($shop);
    }

    /**
     * Add review.
     *
     * @param \iFlair\LetsBonusFrontBundle\Entity\Review $review
     *
     * @return VoucherPrograms
     */
    public function addReview(\iFlair\LetsBonusFrontBundle\Entity\Review $review)
    {
        $this->review[] = $review;

        return $this;
    }

    /**
     * Remove review.
     *
     * @param \iFlair\LetsBonusFrontBundle\Entity\Review $review
     */
    public function removeReview(\iFlair\LetsBonusFrontBundle\Entity\Review $review)
    {
        $this->review->removeElement($review);
    }

    /**
     * Get review.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReview()
    {
        return $this->review;
    }

     /**
     * Add offerSpecial.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\offerSpecials $offerSpecial
     *
     * @return VoucherPrograms
     */
    public function addOfferSpecial(\iFlair\LetsBonusAdminBundle\Entity\offerSpecials $offerSpecial)
    {
        $this->offerSpecials[] = $offerSpecial;

        return $this;
    }

    /**
     * Remove offerSpecial.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\offerSpecials $offerSpecial
     */
    public function removeOfferSpecial(\iFlair\LetsBonusAdminBundle\Entity\offerSpecials $offerSpecial)
    {
        $this->offerSpecials->removeElement($offerSpecial);
    }

    /**
     * Get offerSpecials.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOfferSpecials()
    {
        return $this->offerSpecials;
    }
}
