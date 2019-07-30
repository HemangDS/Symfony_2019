<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tags.
 *
 * @ORM\Table(name="lb_tags")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\TagsRepository")
 */
class Tags
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
     * @ORM\OneToMany(targetEntity="Shop", mappedBy="tag")
     */
    private $shop;

    /**
     * @ORM\OneToMany(targetEntity="shopHistory", mappedBy="tag")
     */
    private $shopHistory;

    public function __toString()
    {
        return strval($this->id);
    }

    public function __construct()
    {
        $this->shopHistory = new ArrayCollection();
        $this->shop = new ArrayCollection();
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    public function getShop()
    {
        return $this->shop;
    }

    public function getShopHistory()
    {
        return $this->shopHistory;
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
     * @return Tags
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
     * @return Tags
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
     * @return Tags
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
     * Add shopHistory.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\shopHistory $shopHistory
     *
     * @return Tags
     */
    public function addShopHistory(\iFlair\LetsBonusAdminBundle\Entity\shopHistory $shopHistory)
    {
        $this->shopHistory[] = $shopHistory;

        return $this;
    }

    /**
     * Remove shopHistory.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\shopHistory $shopHistory
     */
    public function removeShopHistory(\iFlair\LetsBonusAdminBundle\Entity\shopHistory $shopHistory)
    {
        $this->shopHistory->removeElement($shopHistory);
    }
}
