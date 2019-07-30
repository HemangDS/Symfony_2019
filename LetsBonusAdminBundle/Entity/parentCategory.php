<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * parentCategory.
 *
 * @ORM\Table(name="lb_parent_category")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\parentCategoryRepository")
 */
class parentCategory
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    private $nimage;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    private $bannerimage;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    private $logoimage;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parentCategory")
     */
    private $category;

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
     * @ORM\ManyToMany(targetEntity="Shop", mappedBy="parentCategory")
     */
    private $shop;

    /**
     * @ORM\OneToMany(targetEntity="childCategory", mappedBy="parentCategory")
     */
    private $childCategory;

    /**
     * @var string
     *
     * @ORM\Column(name="bannerdescription", type="text")
     */
    private $bannerdescription;

    /**
     * @var string
     *
     * @ORM\Column(name="bannertitle", type="string", length=255)
     */
    private $bannertitle;

    /**
     * @var int
     *
     * @ORM\Column(name="show_on_como_functiona", type="integer", nullable=true)
     */
    private $show_on_como_functiona;

    /**
     * @var int
     *
     * @ORM\Column(name="highlightedHome", type="integer" , nullable=true)
     */
    private $highlightedHome;

    public function __construct()
    {
        $this->shop = new ArrayCollection();
        $this->category = new ArrayCollection();
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    public function getShop()
    {
        return $this->shop;
    }

    public function getCategory()
    {
        return $this->category;
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
     * @return parentCategory
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return parentCategory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set nimage.
     *
     * @param string $nimage
     *
     * @return parentCategory
     */
    public function setnImage($nimage)
    {
        $this->nimage = $nimage;

        return $this;
    }

    /**
     * Get nimage.
     *
     * @return string
     */
    public function getnImage()
    {
        return $this->nimage;
    }

    /**
     * Set bannerimage.
     *
     * @param string $bannerimage
     *
     * @return parentCategory
     */
    public function setBannerImage($bannerimage)
    {
        $this->bannerimage = $bannerimage;

        return $this;
    }

    /**
     * Get bannerimage.
     *
     * @return string
     */
    public function getBannerImage()
    {
        return $this->bannerimage;
    }

    /**
     * Set logoimage.
     *
     * @param string $logoimage
     *
     * @return parentCategory
     */
    public function setLogoimage($logoimage)
    {
        $this->logoimage = $logoimage;

        return $this;
    }

    /**
     * Get logoimage.
     *
     * @return string
     */
    public function getLogoimage()
    {
        return $this->logoimage;
    }

    /**
     * Set status.
     *
     * @param bool $status
     *
     * @return parentCategory
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
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return parentCategory
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
     * @return parentCategory
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
     * Add shop.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Shop $shop
     *
     * @return parentCategory
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
     * Set url.
     *
     * @param string $url
     *
     * @return parentCategory
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
     * Add category.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Category $category
     *
     * @return parentCategory
     */
    public function addCategory(\iFlair\LetsBonusAdminBundle\Entity\Category $category)
    {
        $this->category[] = $category;

        return $this;
    }

    /**
     * Remove category.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Category $category
     */
    public function removeCategory(\iFlair\LetsBonusAdminBundle\Entity\Category $category)
    {
        $this->category->removeElement($category);
    }

    /**
     * Add childCategory.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\childCategory $childCategory
     *
     * @return parentCategory
     */
    public function addChildCategory(\iFlair\LetsBonusAdminBundle\Entity\childCategory $childCategory)
    {
        $this->childCategory[] = $childCategory;

        return $this;
    }

    /**
     * Remove childCategory.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\childCategory $childCategory
     */
    public function removeChildCategory(\iFlair\LetsBonusAdminBundle\Entity\childCategory $childCategory)
    {
        $this->childCategory->removeElement($childCategory);
    }

    /**
     * Get childCategory.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildCategory()
    {
        return $this->childCategory;
    }

    /**
     * Set bannerdescription.
     *
     * @param string $bannerdescription
     *
     * @return parentCategory
     */
    public function setBannerDescription($bannerdescription)
    {
        $this->bannerdescription = $bannerdescription;

        return $this;
    }

    /**
     * Get bannerdescription.
     *
     * @return string
     */
    public function getBannerDescription()
    {
        return $this->bannerdescription;
    }

     /**
     * Set bannertitle.
     *
     * @param string $bannertitle
     *
     * @return parentCategory
     */
    public function setBannerTitle($bannertitle)
    {
        $this->bannertitle = $bannertitle;

        return $this;
    }

    /**
     * Get bannertitle.
     *
     * @return string
     */
    public function getBannerTitle()
    {
        return $this->bannertitle;
    }

    
    /**
     * Set show_on_como_functiona.
     *
     * @param string $show_on_como_functiona
     *
     * @return Slider
     */
    public function setShowOnComoFunctiona($show_on_como_functiona)
    {
        $this->show_on_como_functiona = $show_on_como_functiona;

        return $this;
    }

    /**
     * Get show_on_como_functiona.
     *
     * @return string
     */
    public function getShowOnComoFunctiona()
    {
        return $this->show_on_como_functiona;
    }


    
     /**
     * Set highlightedHome.
     *
     * @param int $highlightedHome
     *
     * @return parentCategory
     */
    public function setHighlightedHome($highlightedHome)
    {
        $this->highlightedHome = $highlightedHome;

        return $this;
    }

    /**
     * Get highlightedHome.
     *
     * @return int
     */
    public function getHighlightedHome()
    {
        return $this->highlightedHome;
    }

}
