<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Language.
 *
 * @ORM\Table(name="lb_language")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\LanguageRepository")
 */
class Language
{
    const DEFAULTLANG = 'EN';
    const DEFAULTLANGNAME = 'English';
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
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code;

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
     * @ORM\OneToMany(targetEntity="Companies", mappedBy="lang")
     */
    private $companies;

    /**
     * @ORM\OneToMany(targetEntity="Voucher", mappedBy="language")
     */
    private $voucher;

    /**
     * @ORM\OneToMany(targetEntity="Country", mappedBy="language")
     */
    private $country;

    public function __toString()
    {
        return strval($this->id);
    }

    public function __construct()
    {
        $this->country = new ArrayCollection();
        $this->companies = new ArrayCollection();
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    public function getCountry()
    {
        return $this->country;
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
     * Set name.
     *
     * @param string $name
     *
     * @return Language
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
     * Set code.
     *
     * @param string $code
     *
     * @return Language
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
     * Add company.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Companies $company
     *
     * @return Language
     */
    public function addCompany(\iFlair\LetsBonusAdminBundle\Entity\Companies $company)
    {
        $this->companies[] = $company;

        return $this;
    }

    /**
     * Remove company.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Companies $company
     */
    public function removeCompany(\iFlair\LetsBonusAdminBundle\Entity\Companies $company)
    {
        $this->companies->removeElement($company);
    }

    /**
     * Add voucher.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Voucher $voucher
     *
     * @return Language
     */
    public function addVoucher(\iFlair\LetsBonusAdminBundle\Entity\Voucher $voucher)
    {
        $this->Voucher[] = $voucher;

        return $this;
    }

    /**
     * Remove voucher.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Voucher $voucher
     */
    public function removeVoucher(\iFlair\LetsBonusAdminBundle\Entity\Voucher $voucher)
    {
        $this->Voucher->removeElement($voucher);
    }

    /**
     * Get voucher.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoucher()
    {
        return $this->Voucher;
    }

    /**
     * Add country.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Country $country
     *
     * @return Language
     */
    public function addCountry(\iFlair\LetsBonusAdminBundle\Entity\Country $country)
    {
        $this->country[] = $country;

        return $this;
    }

    /**
     * Remove country.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Country $country
     */
    public function removeCountry(\iFlair\LetsBonusAdminBundle\Entity\Country $country)
    {
        $this->country->removeElement($country);
    }
}
