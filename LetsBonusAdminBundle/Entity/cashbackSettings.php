<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * cashbackSettings.
 *
 * @ORM\Table(name="lb_cashbackSettings")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\cashbackSettingsRepository")
 */
class cashbackSettings
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
     * @ORM\ManyToOne(targetEntity="Companies", inversedBy="cashbackSettings")
     */
    private $companies;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startDate", type="datetime")
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endDate", type="datetime")
     */
    private $endDate;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="cashbackSettings")
     * @ORM\Column(name="administrator", type="integer")
     */
    private $administrator;

    /**
     * @ORM\ManyToMany(targetEntity="Shop", inversedBy="cashbackSettings")
     * @ORM\JoinTable(
     *          name="lb_cachback_settings_shop",
     *          joinColumns={@ORM\JoinColumn(name="cashback_settings_id", referencedColumnName="id")},
     *          inverseJoinColumns={@ORM\JoinColumn(name="shop_id", referencedColumnName="id")}
     * )
     */
    private $shop;

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
     * @ORM\OneToMany(targetEntity="cashbackTransactions", mappedBy="cashbacksettingId")
     */
    private $cashbackTransactions;

    public function __toString()
    {
        return strval($this->id);
    }

    public function __construct()
    {
        $this->cashbackTransactions = new ArrayCollection();
        $this->shop = new ArrayCollection();
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    public function getCashbackTransactions()
    {
        return $this->cashbackTransactions;
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
     * @return cashbackSettings
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
     * Set companies.
     *
     * @param int $companies
     *
     * @return cashbackSettings
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
     * Set type.
     *
     * @param string $type
     *
     * @return cashbackSettings
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set startDate.
     *
     * @param \DateTime $startDate
     *
     * @return cashbackSettings
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate.
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate.
     *
     * @param \DateTime $endDate
     *
     * @return cashbackSettings
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate.
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set status.
     *
     * @param int $status
     *
     * @return cashbackSettings
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set administrator.
     *
     * @param int $administrator
     *
     * @return cashbackSettings
     */
    public function setAdministrator($administrator)
    {
        $this->administrator = $administrator;

        return $this;
    }

    /**
     * Get administrator.
     *
     * @return int
     */
    public function getAdministrator()
    {
        return $this->administrator;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return cashbackSettings
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
     * @return cashbackSettings
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
     * @return cashbackSettings
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
     * Add cashbackTransaction.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\cashbackTransactions $cashbackTransaction
     *
     * @return cashbackSettings
     */
    public function addCashbackTransaction(\iFlair\LetsBonusAdminBundle\Entity\cashbackTransactions $cashbackTransaction)
    {
        $this->cashbackTransactions[] = $cashbackTransaction;

        return $this;
    }

    /**
     * Remove cashbackTransaction.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\cashbackTransactions $cashbackTransaction
     */
    public function removeCashbackTransaction(\iFlair\LetsBonusAdminBundle\Entity\cashbackTransactions $cashbackTransaction)
    {
        $this->cashbackTransactions->removeElement($cashbackTransaction);
    }
}
