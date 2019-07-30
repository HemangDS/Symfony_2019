<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * VoucherTradeDoublerSiteToken.
 *
 * @ORM\Table(name="lb_voucher_tradedoubler_site_token")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\VoucherTradeDoublerSiteTokenRepository")
 */
class VoucherTradeDoublerSiteToken
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
     * @var int
     *
     * @ORM\Column(name="site_id", type="integer")
     */
    private $siteId;

    /**
     * @var string
     *
     * @ORM\Column(name="site_name", type="string", length=255)
     */
    private $siteName;

    /**
     * @var string
     *
     * @ORM\Column(name="site_token", type="string", length=255)
     */
    private $siteToken;

    /**
     * @ORM\ManyToOne(targetEntity="Companies", inversedBy="voucherTradeDoublerSiteToken")
     */
    private $company;

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
        $this->company = new ArrayCollection();
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
     * Set siteId.
     *
     * @param int $siteId
     *
     * @return VoucherTradeDoublerSiteToken
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;

        return $this;
    }

    /**
     * Get siteId.
     *
     * @return int
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * Set siteName.
     *
     * @param string $siteName
     *
     * @return VoucherTradeDoublerSiteToken
     */
    public function setSiteName($siteName)
    {
        $this->siteName = $siteName;

        return $this;
    }

    /**
     * Get siteName.
     *
     * @return string
     */
    public function getSiteName()
    {
        return $this->siteName;
    }

    /**
     * Set siteToken.
     *
     * @param string $siteToken
     *
     * @return VoucherTradeDoublerSiteToken
     */
    public function setSiteToken($siteToken)
    {
        $this->siteToken = $siteToken;

        return $this;
    }

    /**
     * Get siteToken.
     *
     * @return string
     */
    public function getSiteToken()
    {
        return $this->siteToken;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return VoucherTradeDoublerSiteToken
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
     * @return VoucherTradeDoublerSiteToken
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
     * Set company.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Companies $company
     *
     * @return VoucherTradeDoublerSiteToken
     */
    public function setCompany(\iFlair\LetsBonusAdminBundle\Entity\Companies $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company.
     *
     * @return \iFlair\LetsBonusAdminBundle\Entity\Companies
     */
    public function getCompany()
    {
        return $this->company;
    }
}
