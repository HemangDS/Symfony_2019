<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Advertisement.
 *
 * @ORM\Table(name="lb_advertisement")
 * @ORM\Entity
 */
class Advertisement
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
     * @ORM\Column(name="adv_name", type="string", length=255)
     */
    private $advName;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="AdvertisementType") 
     * @ORM\JoinColumn(name="adv_type", referencedColumnName="id")
     */
    private $advType;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    /**
     * Set advName.
     *
     * @param string $advName
     *
     * @return Advertisement
     */
    public function setAdvName($advName)
    {
        $this->advName = $advName;

        return $this;
    }

    /**
     * Get advName.
     *
     * @return string
     */
    public function getAdvName()
    {
        return $this->advName;
    }

    /**
     * Set image.
     *
     * @param int $image
     *
     * @return Advertisement
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return int
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set advType.
     *
     * @param int $advType
     *
     * @return Advertisement
     */
    public function setAdvType($advType)
    {
        $this->advType = $advType;

        return $this;
    }

    /**
     * Get advType.
     *
     * @return int
     */
    public function getAdvType()
    {
        return $this->advType;
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
     * @return Network
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
     * @return Network
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
     * @return Advertisement
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
