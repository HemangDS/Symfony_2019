<?php

namespace Galop\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * News
 *
 * @ORM\Table(name="news")
 * @ORM\Entity(repositoryClass="Galop\AdminBundle\Repository\NewsRepository")
 */
class News
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
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="short_article", type="text", nullable=true)
     */
    private $shortArticle;

    /**
     * @var string|null
     *
     * @ORM\Column(name="full_article", type="text", nullable=true)
     */
    private $fullArticle;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Gallery
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Gallery", cascade={"persist"}, fetch="LAZY")
     * @Assert\NotBlank(message="Please select atleast one file")
     */
    private $images;



    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    private $document;


    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="NewsCategory", inversedBy="news")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
     */
    private $category;
    
    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="NewsDescipline", inversedBy="news")
     * @ORM\JoinColumn(name="descipline_id", referencedColumnName="id", nullable=true)
     */
    private $descipline;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="NewsRegion", inversedBy="news")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id", nullable=true)
     */
    private $region;

    /**
     * @var array|null
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="news")
     * @ORM\JoinTable(name="news_tag",
     *      joinColumns={@ORM\JoinColumn(name="news_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     *      )
     */
    private $tags;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", options={"default": 1})
     */
    private $status;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_headline", type="boolean")
     */
    private $isHeadline;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_premium", type="boolean")
     */
    private $isPremium;

    /**
     * @var string
     *
     * @ORM\Column(name="watermark_text", type="string", length=255, nullable=true, options={"default": "Paardenfotograaf.be"})
     */
    private $watermarkText;

    /**
     * @var string
     *
     * @ORM\Column(name="photographer", type="string", length=255, nullable=true, options={"default": "Paardenfotograaf.be"})
     */
    private $photographer;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="article_date", type="datetime", nullable=true)
     */
    private $articleDate;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="User", inversedBy="news")
     * @ORM\JoinColumn(name="created_by_user", referencedColumnName="id", nullable=true)
     */
    private $createdByUser;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="User", inversedBy="news1")
     * @ORM\JoinColumn(name="updated_by_user", referencedColumnName="id", nullable=true)
     */
    private $updatedByUser;

    /**
     * @var int
     *
     * @ORM\Column(name="desktop_counter", type="bigint", nullable=true, options={"default": 0})
     */
    private $desktop_counter;

    /**
     * @var int
     *
     * @ORM\Column(name="mobile_counter", type="bigint", nullable=true, options={"default": 0})
     */
    private $mobile_counter;

    /**
     * @var int
     *
     * @ORM\Column(name="tablet_counter", type="bigint", nullable=true, options={"default": 0})
     */
    private $tablet_counter;
    
    public function __toString(){
        return $this->title;    
    }

    public function __construct()
    {
        $this->tags = new ArrayCollection();
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
     * Set title.
     *
     * @param string|null $title
     *
     * @return News
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set shortArticle.
     *
     * @param string|null $shortArticle
     *
     * @return News
     */
    public function setShortArticle($shortArticle = null)
    {
        $this->shortArticle = $shortArticle;

        return $this;
    }

    /**
     * Get shortArticle.
     *
     * @return string|null
     */
    public function getShortArticle()
    {
        return $this->shortArticle;
    }

    /**
     * Set fullArticle.
     *
     * @param string|null $fullArticle
     *
     * @return News
     */
    public function setFullArticle($fullArticle = null)
    {
        $this->fullArticle = $fullArticle;

        return $this;
    }

    /**
     * Get fullArticle.
     *
     * @return string|null
     */
    public function getFullArticle()
    {
        return $this->fullArticle;
    }

    /**
     * Set images.
     *
     * @param string|null $images
     *
     * @return News
     */
    public function setImages($images = null)
    {
        $this->images = $images;

        return $this;
    }

    /**
     * Get images.
     *
     * @return int|null
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set document.
     *
     * @param string $document
     *
     * @return News
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document.
     *
     * @return string
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set category.
     *
     * @param int $category
     *
     * @return News
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set descipline.
     *
     * @param int $descipline
     *
     * @return News
     */
    public function setDescipline($descipline)
    {
        $this->descipline = $descipline;

        return $this;
    }

    /**
     * Get descipline.
     *
     * @return int
     */
    public function getDescipline()
    {
        return $this->descipline;
    }

    /**
     * Set region.
     *
     * @param int $region
     *
     * @return News
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region.
     *
     * @return int
     */
    public function getRegion()
    {
        return $this->region;
    }


    /**
     * Set tags.
     *
     * @param int|null $tags
     *
     * @return Tag
     */
    public function setTags(ArrayCollection $tags)
    {
        
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags.
     *
     * @return int|null
     */
    public function getTags()
    {
        return $this->tags;
    }


    public function addTags(Tag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    public function removeTags(Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Set status.
     *
     * @param bool $status
     *
     * @return News
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
     * Set isHeadline.
     *
     * @param bool $isHeadline
     *
     * @return News
     */
    public function setIsHeadline($isHeadline)
    {
        $this->isHeadline = $isHeadline;

        return $this;
    }

    /**
     * Get isHeadline.
     *
     * @return bool
     */
    public function getIsHeadline()
    {
        return $this->isHeadline;
    }

    /**
     * Set isPremium.
     *
     * @param bool $isPremium
     *
     * @return News
     */
    public function setIsPremium($isPremium)
    {
        $this->isPremium = $isPremium;

        return $this;
    }

    /**
     * Get isPremium.
     *
     * @return bool
     */
    public function getIsPremium()
    {
        return $this->isPremium;
    }

    /**
     * Set watermarkText.
     *
     * @param string $watermarkText
     *
     * @return News
     */
    public function setWatermarkText($watermarkText)
    {
        $this->watermarkText = $watermarkText;

        return $this;
    }

    /**
     * Get watermarkText.
     *
     * @return string
     */
    public function getWatermarkText()
    {
        return $this->watermarkText;
    }

    /**
     * Set photographer.
     *
     * @param string $photographer
     *
     * @return News
     */
    public function setPhotographer($photographer)
    {
        $this->photographer = $photographer;

        return $this;
    }

    /**
     * Get photographer.
     *
     * @return string
     */
    public function getPhotographer()
    {
        return $this->photographer;
    }

    /**
     * Set author.
     *
     * @param string $author
     *
     * @return News
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author.
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return News
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
     * Set updated.
     *
     * @param \DateTime $updated
     *
     * @return News
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated.
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set articleDate.
     *
     * @param \DateTime $articleDate
     *
     * @return News
     */
    public function setArticleDate($articleDate)
    {
        $this->articleDate = $articleDate;

        return $this;
    }

    /**
     * Get articleDate.
     *
     * @return \DateTime
     */
    public function getArticleDate()
    {
        return $this->articleDate;
    }

    /**
     * Set createdByUser.
     *
     * @param int $createdByUser
     *
     * @return News
     */
    public function setCreatedByUser($createdByUser)
    {
        $this->createdByUser = $createdByUser;

        return $this;
    }

    /**
     * Get createdByUser.
     *
     * @return int
     */
    public function getCreatedByUser()
    {
        return $this->createdByUser;
    }

    /**
     * Set updatedByUser.
     *
     * @param int $updatedByUser
     *
     * @return News
     */
    public function setUpdatedByUser($updatedByUser)
    {
        $this->updatedByUser = $updatedByUser;

        return $this;
    }

    /**
     * Get updatedByUser.
     *
     * @return int
     */
    public function getUpdatedByUser()
    {
        return $this->updatedByUser;
    }

    /**
     * Set desktop_counter.
     *
     * @param int|null $desktop_counter
     *
     * @return Advertisement
     */
    public function setDesktopCounter($desktop_counter = null)
    {
        $this->desktop_counter = $desktop_counter;

        return $this;
    }

    /**
     * Get desktop_counter.
     *
     * @return int|null
     */
    public function getDesktopCounter()
    {
        return $this->desktop_counter;
    }

    /**
     * Set mobile_counter.
     *
     * @param int|null $mobile_counter
     *
     * @return Advertisement
     */
    public function setMobileCounter($mobile_counter = null)
    {
        $this->mobile_counter = $mobile_counter;

        return $this;
    }

    /**
     * Get mobile_counter.
     *
     * @return int|null
     */
    public function getMobileCounter()
    {
        return $this->mobile_counter;
    }

    /**
     * Set tablet_counter.
     *
     * @param int|null $tablet_counter
     *
     * @return Advertisement
     */
    public function setTabletCounter($tablet_counter = null)
    {
        $this->tablet_counter = $tablet_counter;

        return $this;
    }

    /**
     * Get tablet_counter.
     *
     * @return int|null
     */
    public function getTabletCounter()
    {
        return $this->tablet_counter;
    }
}
