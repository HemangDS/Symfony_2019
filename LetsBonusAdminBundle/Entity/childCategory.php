<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * childCategory.
 *
 * @ORM\Table(name="lb_child_category")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\childCategoryRepository")
 */
class childCategory
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
     * @var int
     * @ORM\ManyToOne(targetEntity="parentCategory", inversedBy="childCategory")	
     * @ORM\JoinColumn(name="parent_category_id", referencedColumnName="id")
     */
    private $parentCategory;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="childCategory",  cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $Category;

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
     * @ORM\ManyToMany(targetEntity="Shop", mappedBy="childCategory")
     */
    private $shop;

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

    public function setCategory(Category $Category)
    {
        $this->Category = $Category;
    }

    public function getCategory()
    {
        return $this->Category;
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
     * @return childCategory
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
     * @return childCategory
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
     * @return childCategory
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
     * @return childCategory
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
     * @return childCategory
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
     * @return childCategory
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
     * Set parentCategory.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\parentCategory $parentCategory
     *
     * @return childCategory
     */
    public function setParentCategory(\iFlair\LetsBonusAdminBundle\Entity\parentCategory $parentCategory = null)
    {
        $this->parentCategory = $parentCategory;

        return $this;
    }

    /**
     * Get parentCategory.
     *
     * @return \iFlair\LetsBonusAdminBundle\Entity\parentCategory
     */
    public function getParentCategory()
    {
        return $this->parentCategory;
    }

    /**
     * Set bannerdescription.
     *
     * @param string $bannerdescription
     *
     * @return childCategory
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
     * @return childCategory
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
     * @return childCategory
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
