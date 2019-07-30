<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * NewsletterBanner.
 *
 * @ORM\Table(name="lb_newsletter_banner")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\NewsletterBannerRepository")
 */
class NewsletterBanner
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
     * @ORM\Column(name="bannername", type="string", length=255)
     */
    private $bannername;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var int
     *
     * @ORM\Column(name="firstbanner", type="integer")
     */
    private $firstbanner;

    /**
     * @var string
     *
     * @ORM\Column(name="dfpcode", type="text", nullable=true)
     */
    private $dfpcode;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    private $image;

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
        $this->variation = new ArrayCollection();
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    public function __toString()
    {
        return $this->bannername;
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
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return NewsletterBanner
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
     * @return NewsletterBanner
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
     * Set url.
     *
     * @param string $url
     *
     * @return NewsletterBanner
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
     * Set image.
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $image
     *
     * @return NewsletterBanner
     */
    public function setImage(\Application\Sonata\MediaBundle\Entity\Media $image = null)
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
     * Set bannername.
     *
     * @param string $bannername
     *
     * @return NewsletterBanner
     */
    public function setBannername($bannername)
    {
        $this->bannername = $bannername;

        return $this;
    }

    /**
     * Get bannername.
     *
     * @return string
     */
    public function getBannername()
    {
        return $this->bannername;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return NewsletterBanner
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
     * Set firstbanner.
     *
     * @param int $firstbanner
     *
     * @return NewsletterBanner
     */
    public function setFirstbanner($firstbanner)
    {
        $this->firstbanner = $firstbanner;

        return $this;
    }

    /**
     * Get firstbanner.
     *
     * @return int
     */
    public function getFirstbanner()
    {
        return $this->firstbanner;
    }

    /**
     * Set dfpcode.
     *
     * @param string $dfpcode
     *
     * @return NewsletterBanner
     */
    public function setDfpcode($dfpcode)
    {
        $this->dfpcode = $dfpcode;

        return $this;
    }

    /**
     * Get dfpcode.
     *
     * @return string
     */
    public function getDfpcode()
    {
        return $this->dfpcode;
    }
}
