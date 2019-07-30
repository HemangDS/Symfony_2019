<?php

namespace Galop\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ApiArticle
 *
 * @ORM\Table(name="api_article")
 * @ORM\Entity(repositoryClass="Galop\AdminBundle\Repository\ApiArticleRepository")
 */
class ApiArticle
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
     * @ORM\Column(name="article_id", type="string", nullable=true, unique=true)
     */
    protected $ArticleID;

    /**
     * @var string
     *
     * @ORM\Column(name="clientkey", type="string", nullable=true)
     */
    protected $clientkey;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="ApiUser", inversedBy="apiusers")
     * @ORM\JoinColumn(name="api_userid", referencedColumnName="id", nullable=true)
     */
    
    protected $ApiuserId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $ArticleTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="article_link", type="string", length=255, nullable=true)
     */
    protected $ArticleLink;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="article_image", referencedColumnName="id", nullable=true)
     */
    protected $ArticleImage;


    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __toString(){
        return $this->ArticleTitle;    
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
     * Set articleTitle.
     *
     * @param string|null $articleTitle
     *
     * @return ApiArticle
     */
    public function setArticleTitle($articleTitle = null)
    {
        $this->ArticleTitle = $articleTitle;

        return $this;
    }

    /**
     * Get articleTitle.
     *
     * @return string|null
     */
    public function getArticleTitle()
    {
        return $this->ArticleTitle;
    }

    /**
     * Set articleLink.
     *
     * @param string|null $articleLink
     *
     * @return ApiArticle
     */
    public function setArticleLink($articleLink = null)
    {
        $this->ArticleLink = $articleLink;

        return $this;
    }

    /**
     * Get articleLink.
     *
     * @return string|null
     */
    public function getArticleLink()
    {
        return $this->ArticleLink;
    }

     /**
     * Get articleImage.
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media|null
     */
    public function getArticleImage()
    {
        return $this->ArticleImage;
    }

     /**
     * Set articleImage.
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media|null $articleImage
     *
     * @return ApiArticle
     */
    public function setArticleImage(\Application\Sonata\MediaBundle\Entity\Media $articleImage = null)
    {
        $this->ArticleImage = $articleImage;

        return $this;
    }   

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return ApiArticle
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set articleID.
     *
     * @param string|null $articleID
     *
     * @return ApiArticle
     */
    public function setArticleID($articleID = null)
    {
        $this->ArticleID = $articleID;

        return $this;
    }

    /**
     * Get articleID.
     *
     * @return string|null
     */
    public function getArticleID()
    {
        return $this->ArticleID;
    }

    /**
     * Set clientkey.
     *
     * @param string|null $clientkey
     *
     * @return ApiArticle
     */
    public function setClientkey($clientkey = null)
    {
        $this->clientkey = $clientkey;

        return $this;
    }

    /**
     * Get clientkey.
     *
     * @return string|null
     */
    public function getClientkey()
    {
        return $this->clientkey;
    }

    /**
     * Set apiuserId.
     *
     * @param \Galop\AdminBundle\Entity\ApiUser|null $apiuserId
     *
     * @return ApiArticle
     */
    public function setApiuserId(\Galop\AdminBundle\Entity\ApiUser $apiuserId = null)
    {
        $this->ApiuserId = $apiuserId;

        return $this;
    }

    /**
     * Get apiuserId.
     *
     * @return \Galop\AdminBundle\Entity\ApiUser|null
     */
    public function getApiuserId()
    {
        return $this->ApiuserId;
    }
}
