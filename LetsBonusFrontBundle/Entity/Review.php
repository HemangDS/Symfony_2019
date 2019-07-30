<?php

namespace iFlair\LetsBonusFrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Review.
 *
 * @ORM\Table(name="lb_review")
 * @ORM\Entity
 */
class Review
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
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="iFlair\LetsBonusAdminBundle\Entity\FrontUser", inversedBy="review")
     * @ORM\JoinColumn(name = "user_id", referencedColumnName = "id")
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="iFlair\LetsBonusAdminBundle\Entity\Shop", inversedBy="review")
     * @ORM\JoinColumn(name = "shop_id", referencedColumnName = "id")
     */
    private $shopId;

    /**
     * @ORM\ManyToOne(targetEntity="iFlair\LetsBonusAdminBundle\Entity\shopHistory", inversedBy="review")
     * @ORM\JoinColumn(name = "shop_history_id", referencedColumnName = "id")
     */
    private $shopHistoryId;

    /**
     * @ORM\ManyToOne(targetEntity="iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms", inversedBy="review")
     * @ORM\JoinColumn(name = "brand_id", referencedColumnName = "id")
     */
    private $brandId;

    /**
     * @var string
     *
     * @ORM\Column(name="review", type="string", length=255)
     */
    private $review;

    /**
     * @var string
     *
     * @ORM\Column(name="rating", type="string", length=255)
     */
    private $rating;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    public function __toString()
    {
        return strval($this->id);
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
     * Set username.
     *
     * @param string $username
     *
     * @return Review
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Review
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set userId.
     *
     * @param int $userId
     *
     * @return Review
     */
    public function setUserId(\iFlair\LetsBonusAdminBundle\Entity\FrontUser $userId)
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
     * @return Review
     */
    public function setShopId(\iFlair\LetsBonusAdminBundle\Entity\Shop $shopId)
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
     * @return Review
     */
    public function setShopHistoryId(\iFlair\LetsBonusAdminBundle\Entity\shopHistory $shopHistoryId)
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
     * Set brandId.
     *
     * @param int $brandId
     *
     * @return Review
     */
    public function setBrandId(\iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms $brandId)
    {
        $this->brandId = $brandId;

        return $this;
    }

    /**
     * Get brandId.
     *
     * @return int
     */
    public function getBrandId()
    {
        return $this->brandId;
    }

    /**
     * Set review.
     *
     * @param string $review
     *
     * @return Review
     */
    public function setReview($review)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * Get review.
     *
     * @return string
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set rating.
     *
     * @param string $rating
     *
     * @return Review
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating.
     *
     * @return string
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return FrontUser
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
}
