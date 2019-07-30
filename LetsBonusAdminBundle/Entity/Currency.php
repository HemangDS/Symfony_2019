<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Currency.
 *
 * @ORM\Table(name="lb_currency")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\CurrencyRepository")
 */
class Currency
{
    const DEFAULTCURRENCY = 'EUR';
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
     * @ORM\Column(name="code", type="string", length=3, unique=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="symbol", type="string", length=5, nullable=true)
     */
    private $symbol;
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
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Companies", mappedBy="currency",fetch="EXTRA_LAZY")
     */
    private $companies;

    /**
     * @ORM\OneToMany(targetEntity="cashbackTransactions", mappedBy="currency",fetch="EXTRA_LAZY")
     */
    private $cashbackTransactions;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Voucher", mappedBy="currency",fetch="EXTRA_LAZY")
     */
    private $voucher;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="LetsBonusTransactions", mappedBy="currency",fetch="EXTRA_LAZY")
     */
    private $letsbonusTransactions;

    public function __toString()
    {
        return (string) $this->id;
    }

    public function __construct()
    {
        $this->cashbackTransactions = new ArrayCollection();
        $this->companies = new ArrayCollection();
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    public function getCashbackTransactions()
    {
        return $this->cashbackTransactions;
    }

    public function getCompanies()
    {
        return $this->companies;
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
     * Set code.
     *
     * @param string $code
     *
     * @return Currency
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return $this
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
     * @return $this
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
     * Add company.
     *
     * @param Companies $company
     *
     * @return $this
     */
    public function addCompany(Companies $company)
    {
        $this->companies[] = $company;

        return $this;
    }

    /**
     * Remove company.
     *
     * @param Companies $company
     */
    public function removeCompany(Companies $company)
    {
        $this->companies->removeElement($company);
    }

    /**
     * Add cashbackTransaction.
     *
     * @param cashbackTransactions $cashbackTransaction
     *
     * @return $this
     */
    public function addCashbackTransaction(cashbackTransactions $cashbackTransaction)
    {
        $this->cashbackTransactions[] = $cashbackTransaction;

        return $this;
    }

    /**
     * Remove cashbackTransaction.
     *
     * @param cashbackTransactions $cashbackTransaction
     */
    public function removeCashbackTransaction(cashbackTransactions $cashbackTransaction)
    {
        $this->cashbackTransactions->removeElement($cashbackTransaction);
    }

    /**
     * Add voucher.
     *
     * @param Voucher $voucher
     *
     * @return $this
     */
    public function addVoucher(Voucher $voucher)
    {
        $this->voucher[] = $voucher;

        return $this;
    }

    /**
     * Remove voucher.
     *
     * @param Voucher $voucher
     */
    public function removeVoucher(Voucher $voucher)
    {
        $this->voucher->removeElement($voucher);
    }

    /**
     * Get voucher.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoucher()
    {
        return $this->voucher;
    }

    /**
     * Add letsbonusTransaction.
     *
     * @param LetsBonusTransactions $letsbonusTransaction
     *
     * @return Currency
     */
    public function addLetsbonusTransaction(LetsBonusTransactions $letsbonusTransaction)
    {
        $this->letsbonusTransactions[] = $letsbonusTransaction;

        return $this;
    }

    /**
     * Remove letsbonusTransaction.
     *
     * @param LetsBonusTransactions $letsbonusTransaction
     */
    public function removeLetsbonusTransaction(LetsBonusTransactions $letsbonusTransaction)
    {
        $this->letsbonusTransactions->removeElement($letsbonusTransaction);
    }

    /**
     * Get letsbonusTransactions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLetsbonusTransactions()
    {
        return $this->letsbonusTransactions;
    }

    /**
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @param string $symbol
     *
     * @return $this
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;

        return $this;
    }
}
