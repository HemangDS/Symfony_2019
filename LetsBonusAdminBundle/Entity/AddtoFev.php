<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AddtoFev.
 *
 * @ORM\Table(name="lb_add_to_fev")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\AddtoFevRepository")
 */
class AddtoFev
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
     * @ORM\ManyToOne(targetEntity="FrontUser", inversedBy="addtofavUser")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="Shop", inversedBy="addtofav")
     * @ORM\JoinColumn(name="shop_id", referencedColumnName="id")
     */
    private $shopId;

    /**
     * @ORM\ManyToOne(targetEntity="shopHistory", inversedBy="addtofav")
     * @ORM\JoinColumn(name="shop_history_id", referencedColumnName="id")
     */
    private $shopHistoryId;

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

    public function __toString()
    {
        return strval($this->id);
    }

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set userId.
     *
     * @param int $userId
     *
     * @return AddtoFev
     */
    public function setUserId(FrontUser $userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set shopId.
     *
     * @param int $shopId
     *
     * @return AddtoFev
     */
    public function setShopId(Shop $shopId)
    {
        $this->shopId = $shopId;

        return $this;
    }

    /**
     * Get shopId.
     *
     * @return int
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * Set shopHistoryId.
     *
     * @param int $shopHistoryId
     *
     * @return AddtoFev
     */
    public function setShopHistoryId(shopHistory $shopHistoryId)
    {
        $this->shopHistoryId = $shopHistoryId;

        return $this;
    }

    /**
     * Get shopHistoryId.
     *
     * @return int
     */
    public function getShopHistoryId()
    {
        return $this->shopHistoryId;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return AddtoFev
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
     * @return AddtoFev
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
