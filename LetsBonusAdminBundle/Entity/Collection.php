<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Collection.
 *
 * @ORM\Table(name="lb_collection")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\CollectionRepository")
 */
class Collection
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
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @var bool
     *
     * @ORM\Column(name="show_in_front", type="boolean")
     */
    private $show_in_front;

    /**
     * @var bool
     *
     * @ORM\Column(name="mark_special", type="boolean")
     */
    private $mark_special;

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
     * @ORM\ManyToMany(targetEntity="Shop", mappedBy="collections")
     */
    private $shop;

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
     * @return Collection
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
     * @return Collection
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
     * Set url.
     *
     * @param string $url
     *
     * @return Collection
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
     * Set status.
     *
     * @param bool $status
     *
     * @return Collection
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
     * Set show_in_front.
     *
     * @param bool $show_in_front
     *
     * @return Collection
     */
    public function setShowInFront($show_in_front)
    {
        $this->show_in_front = $show_in_front;

        return $this;
    }

    /**
     * Get show_in_front.
     *
     * @return bool
     */
    public function getShowInFront()
    {
        return $this->show_in_front;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Collection
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
     * @return Collection
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
     * @return Collection
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
     * Set markSpecial.
     *
     * @param bool $markSpecial
     *
     * @return Collection
     */
    public function setMarkSpecial($markSpecial)
    {
        $this->mark_special = $markSpecial;

        return $this;
    }

    /**
     * Get markSpecial.
     *
     * @return bool
     */
    public function getMarkSpecial()
    {
        return $this->mark_special;
    }
}
