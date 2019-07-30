<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Network.
 *
 * @ORM\Table(name="lb_network")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\NetworkRepository")
 */
class Network
{
    const TRADEDOUBLER = 'TradeDoubler';
    const TRADEDOUBLERURL = 'http://tradedoubler.com';
    const ZANOX = 'Zanox';
    const ZANOXURL = 'Zanox';
    const TDI = 'TDI';
    const TDIURL = 'TDI';
    const EBAY = 'eBay';
    const EBAYURL = 'eBay';
    const LINKSHAREOLD = 'Linkshare Old';
    const LINKSHAREOLDURL = 'Linkshare Old';
    const LINKSHARE = 'LinkShare';
    const LINKSHAREURL = 'LinkShare';
    const WEBGAINS = 'Webgains';
    const WEBGAINSURL = 'Webgains';
    const CJ = 'CJ';
    const CJURL = 'CJ';
    const AMAZON = 'Amazon';
    const AMAZONURL = 'Amazon';

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
     * @var \DateTime
     *category
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
     * @ORM\OneToMany(targetEntity="Shop", mappedBy="network", cascade={"persist"})    
     */
    private $shop;

    /**
     * @ORM\OneToMany(targetEntity="VoucherPrograms", mappedBy="network")
     */
    private $program;

    /**
     * @ORM\OneToMany(targetEntity="Voucher", mappedBy="network")
     */
    private $voucher;

    private $cashbackSettings;

    /**
     * @ORM\OneToMany(targetEntity="cashbackTransactions", mappedBy="networkId")
     */
    private $cashbackTransactions;

    private $parentCategory;
    /**
     * @ORM\ManyToMany(targetEntity="Country", inversedBy="network")
     * @ORM\JoinTable(name="lb_network_country",
     *      joinColumns={@ORM\JoinColumn(name="network_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="country_id", referencedColumnName="id")}
     *      )
     */
    private $country;

    /**
     * @ORM\OneToMany(targetEntity="networkCredentials", mappedBy="network")
     */
    private $networkCredentials;

    /**
     * @ORM\OneToMany(targetEntity="LetsBonusTransactions", mappedBy="network")
     */
    private $letsbonusTransactions;

    public function __toString()
    {
        return strval($this->id);
    }

    public function __construct()
    {
        $this->networkCredentials = new ArrayCollection();
        $this->country = new ArrayCollection();
        $this->cashbackTransactions = new ArrayCollection();
        $this->shop = new ArrayCollection();
        $this->program = new ArrayCollection();
        $this->voucher = new ArrayCollection();
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    public function getNetworkCredentials()
    {
        return $this->networkCredentials;
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
     * Set id.
     *
     * @param int $id
     *
     * @return id
     */
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
     * Set name.
     *
     * @param string $name
     *
     * @return Network
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
     * @return Network
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
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Network
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
     * @return Network
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
     * @return Network
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
     * Add program.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms $program
     *
     * @return Network
     */
    public function addProgram(\iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms $program)
    {
        $this->program[] = $program;

        return $this;
    }

    /**
     * Remove program.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms $program
     */
    public function removeProgram(\iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms $program)
    {
        $this->program->removeElement($program);
    }

    /**
     * Get program.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * Add voucher.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Voucher $voucher
     *
     * @return Network
     */
    public function addVoucher(\iFlair\LetsBonusAdminBundle\Entity\Voucher $voucher)
    {
        $this->voucher[] = $voucher;

        return $this;
    }

    /**
     * Remove voucher.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Voucher $voucher
     */
    public function removeVoucher(\iFlair\LetsBonusAdminBundle\Entity\Voucher $voucher)
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
     * Add cashbackTransaction.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\cashbackTransactions $cashbackTransaction
     *
     * @return Network
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

    /**
     * Add country.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Country $country
     *
     * @return Network
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
    public function removeCountry(\iFlair\LetsBonusAdminBundle\Entity\country $country)
    {
        $this->country->removeElement($country);
    }

    public function setCountry(Country $country)
    {
        $this->country = $country;
    }

    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Add networkCredential.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\networkCredentials $networkCredential
     *
     * @return Network
     */
    public function addNetworkCredential(\iFlair\LetsBonusAdminBundle\Entity\networkCredentials $networkCredential)
    {
        $this->networkCredentials[] = $networkCredential;

        return $this;
    }

    /**
     * Remove networkCredential.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\networkCredentials $networkCredential
     */
    public function removeNetworkCredential(\iFlair\LetsBonusAdminBundle\Entity\networkCredentials $networkCredential)
    {
        $this->networkCredentials->removeElement($networkCredential);
    }

    /**
     * Add letsbonusTransaction.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions $letsbonusTransaction
     *
     * @return Network
     */
    public function addLetsbonusTransaction(\iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions $letsbonusTransaction)
    {
        $this->letsbonusTransactions[] = $letsbonusTransaction;

        return $this;
    }

    /**
     * Remove letsbonusTransaction.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions $letsbonusTransaction
     */
    public function removeLetsbonusTransaction(\iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions $letsbonusTransaction)
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
}
