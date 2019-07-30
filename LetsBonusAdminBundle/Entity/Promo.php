<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Promo.
 *
 * @ORM\Table(name="lb_promo")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\PromoRepository")
 */
class Promo
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
     * @ORM\ManyToOne(targetEntity="Companies", inversedBy="promo")
     */
    private $companies;

    /**
     * @ORM\ManyToOne(targetEntity="Shop", inversedBy="promo")
     */
    private $shop;

    /**
     * @var int
     *
     * @ORM\Column(name="daysToApprove", type="integer")
     */
    private $daysToApprove;

    /**
     * @var string
     *
     * @ORM\Column(name="transactionAmount", type="decimal", precision=10, scale=2)
     */
    private $transactionAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="users", type="text")
     */
    private $users;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="text")
     */
    private $comments;

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
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
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
     * Set companies.
     *
     * @param int $companies
     *
     * @return Promo
     */
    public function setCompanies(Companies $companies)
    {
        $this->companies = $companies;

        return $this;
    }

    /**
     * Get companies.
     *
     * @return int
     */
    public function getCompanies()
    {
        return $this->companies;
    }

    /**
     * Set shop.
     *
     * @param int $shop
     *
     * @return Promo
     */
    public function setShop(Shop $shop)
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * Get shop.
     *
     * @return int
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * Set daysToApprove.
     *
     * @param int $daysToApprove
     *
     * @return Promo
     */
    public function setDaysToApprove($daysToApprove)
    {
        $this->daysToApprove = $daysToApprove;

        return $this;
    }

    /**
     * Get daysToApprove.
     *
     * @return int
     */
    public function getDaysToApprove()
    {
        return $this->daysToApprove;
    }

    /**
     * Set transactionAmount.
     *
     * @param string $transactionAmount
     *
     * @return Promo
     */
    public function setTransactionAmount($transactionAmount)
    {
        $this->transactionAmount = $transactionAmount;

        return $this;
    }

    /**
     * Get transactionAmount.
     *
     * @return string
     */
    public function getTransactionAmount()
    {
        return $this->transactionAmount;
    }

    /**
     * Set users.
     *
     * @param string $users
     *
     * @return Promo
     */
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get users.
     *
     * @return string
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set comments.
     *
     * @param string $comments
     *
     * @return Promo
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments.
     *
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Promo
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
     * @return Promo
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
