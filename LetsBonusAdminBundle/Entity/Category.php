<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Category.
 *
 * @ORM\Table(name="lb_category")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\CategoryRepository")
 */
class Category
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
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    private $nimage;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="parentCategory", inversedBy="category", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="parent_category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parentCategory;

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
     * @ORM\ManyToMany(targetEntity="Shop", mappedBy="categories", cascade={"persist","remove"})
     */
    private $shop;

    /**
     * @ORM\OneToMany(targetEntity="childCategory", mappedBy="Category")
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
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    public function __toString()
    {
        return strval($this->id);
    }

    public function __construct()
    {
        $this->shop = new ArrayCollection();
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    public function getShop()
    {
        return $this->shop;
    }

    public function setParentCategory(ParentCategory $parentCategory)
    {
        $this->parentCategory = $parentCategory;
    }

    public function getParentCategory()
    {
        return $this->parentCategory;
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
     * Set name.
     *
     * @param string $name
     *
     * @return Category
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
     * @return Category
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
     * Set status.
     *
     * @param bool $status
     *
     * @return Category
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
     * @return Category
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
     * @return Category
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
     * @return Category
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
     * Add childCategory.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Category $childCategory
     *
     * @return Category
     */
    public function addChildCategory(\iFlair\LetsBonusAdminBundle\Entity\Category $childCategory)
    {
        $this->childCategory[] = $childCategory;

        return $this;
    }

    /**
     * Remove childCategory.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Category $childCategory
     */
    public function removeChildCategory(\iFlair\LetsBonusAdminBundle\Entity\Category $childCategory)
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
     * @return Category
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
     * @return Category
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
     * Set url.
     *
     * @param string $url
     *
     * @return Category
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
}
