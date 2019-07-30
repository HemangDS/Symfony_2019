<?php

namespace iFlair\LetsBonusMigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Categories.
 *
 * @ORM\Table(name="categories", indexes={@ORM\Index(name="n1_categories", columns={"parent_id"}), @ORM\Index(name="slug", columns={"slug"}), @ORM\Index(name="company_id", columns={"company_id"})})
 * @ORM\Entity
 */
class Categories
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @var int
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parentId;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status = '1';

    /**
     * @var int
     *
     * @ORM\Column(name="company_id", type="integer", nullable=true)
     */
    private $companyId = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="headerimage", type="string", length=255, nullable=true)
     */
    private $headerimage;

    /**
     * @var string
     *
     * @ORM\Column(name="headerimagelink", type="string", length=255, nullable=true)
     */
    private $headerimagelink;

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
     * Set name.
     *
     * @param string $name
     *
     * @return Categories
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
     * Set slug.
     *
     * @param string $slug
     *
     * @return Categories
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set parentId.
     *
     * @param int $parentId
     *
     * @return Categories
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId.
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set status.
     *
     * @param bool $status
     *
     * @return Categories
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
     * Set companyId.
     *
     * @param int $companyId
     *
     * @return Categories
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
     * Set headerimage.
     *
     * @param string $headerimage
     *
     * @return Categories
     */
    public function setHeaderimage($headerimage)
    {
        $this->headerimage = $headerimage;

        return $this;
    }

    /**
     * Get headerimage.
     *
     * @return string
     */
    public function getHeaderimage()
    {
        return $this->headerimage;
    }

    /**
     * Set headerimagelink.
     *
     * @param string $headerimagelink
     *
     * @return Categories
     */
    public function setHeaderimagelink($headerimagelink)
    {
        $this->headerimagelink = $headerimagelink;

        return $this;
    }

    /**
     * Get headerimagelink.
     *
     * @return string
     */
    public function getHeaderimagelink()
    {
        return $this->headerimagelink;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Categories
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
     * @return Categories
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
