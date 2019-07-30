<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * networkCredentials.
 *
 * @ORM\Table(name="lb_network_credentials")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\networkCredentialsRepository")
 */
class networkCredentials
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
     * @ORM\ManyToOne(targetEntity="Network", inversedBy="networkCredentials")
     */
    private $network;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="amazonUsername", type="string", length=255, nullable=true)
     */
    private $amazonUsername;

    /**
     * @var string
     *
     * @ORM\Column(name="amazonPassword", type="string", length=255, nullable=true)
     */
    private $amazonPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="cjkey", type="text", nullable=true)
     */
    private $cjKey;

    /**
     * @var string
     *
     * @ORM\Column(name="cjurl", type="string", length=255, nullable=true)
     */
    private $cjUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="ebayUsername", type="string", length=255, nullable=true)
     */
    private $ebayUsername;

    /**
     * @var string
     *
     * @ORM\Column(name="ebayPassword", type="string", length=255, nullable=true)
     */
    private $ebayPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="tdtiAffiliateId", type="string", length=255, nullable=true)
     */
    private $tdtiAffiliateId;

    /**
     * @var string
     *
     * @ORM\Column(name="tdtiKey", type="string", length=255, nullable=true)
     */
    private $tdtiKey;

    /**
     * @var string
     *
     * @ORM\Column(name="tdtiUrl", type="string", length=255, nullable=true)
     */
    private $tdtiUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="tradedoublerAffiliateId", type="string", length=255, nullable=true)
     */
    private $tradedoublerAffiliateId;

    /**
     * @var string
     *
     * @ORM\Column(name="tradedoublerKey", type="string", length=255, nullable=true)
     */
    private $tradedoublerKey;

    /**
     * @var string
     *
     * @ORM\Column(name="tradedoublerUrl", type="string", length=255, nullable=true)
     */
    private $tradedoublerUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="webgainsUsername", type="string", length=255, nullable=true)
     */
    private $webgainsUsername;

    /**
     * @var string
     *
     * @ORM\Column(name="webgainsPassword", type="string", length=255, nullable=true)
     */
    private $webgainsPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="webgainsCampaignId", type="string", length=255, nullable=true)
     */
    private $webgainsCampaignId;

    /**
     * @var string
     *
     * @ORM\Column(name="webgainsLocation", type="string", length=255, nullable=true)
     */
    private $webgainsLocation;

    /**
     * @var string
     *
     * @ORM\Column(name="webgainsUri", type="string", length=255, nullable=true)
     */
    private $webgainsUri;

    /**
     * @var string
     *
     * @ORM\Column(name="zenoxConnectId", type="string", length=255, nullable=true)
     */
    private $zenoxConnectId;

    /**
     * @var string
     *
     * @ORM\Column(name="zenoxSecretKey", type="string", length=255, nullable=true)
     */
    private $zenoxSecretKey;

    /**
     * @var string
     *
     * @ORM\Column(name="zenoxRegion", type="string", length=255, nullable=true)
     */
    private $zenoxRegion;

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
     * @ORM\OneToMany(targetEntity="AmazonCredentials", mappedBy="networkCredentials", cascade={"persist", "remove"})
     */
    private $amazonCredentials;

    public function __construct()
    {
        $this->amazonCredentials = new ArrayCollection();
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
     * Set network.
     *
     * @param int $network
     *
     * @return networkCredentials
     */
    public function setNetwork(Network $network)
    {
        $this->network = $network;

        return $this;
    }

    /**
     * Get network.
     *
     * @return int
     */
    public function getNetwork()
    {
        return $this->network;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return networkCredentials
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set amazonUsername.
     *
     * @param string $amazonUsername
     *
     * @return networkCredentials
     */
    public function setAmazonUsername($amazonUsername)
    {
        $this->amazonUsername = $amazonUsername;

        return $this;
    }

    /**
     * Get amazonUsername.
     *
     * @return string
     */
    public function getAmazonUsername()
    {
        return $this->amazonUsername;
    }

    /**
     * Set amazonPassword.
     *
     * @param string $amazonPassword
     *
     * @return networkCredentials
     */
    public function setAmazonPassword($amazonPassword)
    {
        $this->amazonPassword = $amazonPassword;

        return $this;
    }

    /**
     * Get amazonPassword.
     *
     * @return string
     */
    public function getAmazonPassword()
    {
        return $this->amazonPassword;
    }

    /**
     * Set cjKey.
     *
     * @param string $cjKey
     *
     * @return networkCredentials
     */
    public function setCjKey($cjKey)
    {
        $this->cjKey = $cjKey;

        return $this;
    }

    /**
     * Get cjKey.
     *
     * @return string
     */
    public function getCjKey()
    {
        return $this->cjKey;
    }

    /**
     * Set cjUrl.
     *
     * @param string $cjUrl
     *
     * @return networkCredentials
     */
    public function setCjUrl($cjUrl)
    {
        $this->cjUrl = $cjUrl;

        return $this;
    }

    /**
     * Get cjUrl.
     *
     * @return string
     */
    public function getCjUrl()
    {
        return $this->cjUrl;
    }

    /**
     * Set ebayUsername.
     *
     * @param string $ebayUsername
     *
     * @return networkCredentials
     */
    public function setEbayUsername($ebayUsername)
    {
        $this->ebayUsername = $ebayUsername;

        return $this;
    }

    /**
     * Get ebayUsername.
     *
     * @return string
     */
    public function getEbayUsername()
    {
        return $this->ebayUsername;
    }

    /**
     * Set ebayPassword.
     *
     * @param string $ebayPassword
     *
     * @return networkCredentials
     */
    public function setEbayPassword($ebayPassword)
    {
        $this->ebayPassword = $ebayPassword;

        return $this;
    }

    /**
     * Get ebayPassword.
     *
     * @return string
     */
    public function getEbayPassword()
    {
        return $this->ebayPassword;
    }

    /**
     * Set tdtiAffiliateId.
     *
     * @param string $tdtiAffiliateId
     *
     * @return networkCredentials
     */
    public function setTdtiAffiliateId($tdtiAffiliateId)
    {
        $this->tdtiAffiliateId = $tdtiAffiliateId;

        return $this;
    }

    /**
     * Get tdtiAffiliateId.
     *
     * @return string
     */
    public function getTdtiAffiliateId()
    {
        return $this->tdtiAffiliateId;
    }

    /**
     * Set tdtiKey.
     *
     * @param string $tdtiKey
     *
     * @return networkCredentials
     */
    public function setTdtiKey($tdtiKey)
    {
        $this->tdtiKey = $tdtiKey;

        return $this;
    }

    /**
     * Get tdtiKey.
     *
     * @return string
     */
    public function getTdtiKey()
    {
        return $this->tdtiKey;
    }

    /**
     * Set tdtiUrl.
     *
     * @param string $tdtiUrl
     *
     * @return networkCredentials
     */
    public function setTdtiUrl($tdtiUrl)
    {
        $this->tdtiUrl = $tdtiUrl;

        return $this;
    }

    /**
     * Get tdtiUrl.
     *
     * @return string
     */
    public function getTdtiUrl()
    {
        return $this->tdtiUrl;
    }

    /**
     * Set tradedoublerAffiliateId.
     *
     * @param string $tradedoublerAffiliateId
     *
     * @return networkCredentials
     */
    public function setTradedoublerAffiliateId($tradedoublerAffiliateId)
    {
        $this->tradedoublerAffiliateId = $tradedoublerAffiliateId;

        return $this;
    }

    /**
     * Get tradedoublerAffiliateId.
     *
     * @return string
     */
    public function getTradedoublerAffiliateId()
    {
        return $this->tradedoublerAffiliateId;
    }

    /**
     * Set tradedoublerKey.
     *
     * @param string $tradedoublerKey
     *
     * @return networkCredentials
     */
    public function setTradedoublerKey($tradedoublerKey)
    {
        $this->tradedoublerKey = $tradedoublerKey;

        return $this;
    }

    /**
     * Get tradedoublerKey.
     *
     * @return string
     */
    public function getTradedoublerKey()
    {
        return $this->tradedoublerKey;
    }

    /**
     * Set tradedoublerUrl.
     *
     * @param string $tradedoublerUrl
     *
     * @return networkCredentials
     */
    public function setTradedoublerUrl($tradedoublerUrl)
    {
        $this->tradedoublerUrl = $tradedoublerUrl;

        return $this;
    }

    /**
     * Get tradedoublerUrl.
     *
     * @return string
     */
    public function getTradedoublerUrl()
    {
        return $this->tradedoublerUrl;
    }

    /**
     * Set webgainsUsername.
     *
     * @param string $webgainsUsername
     *
     * @return networkCredentials
     */
    public function setWebgainsUsername($webgainsUsername)
    {
        $this->webgainsUsername = $webgainsUsername;

        return $this;
    }

    /**
     * Get webgainsUsername.
     *
     * @return string
     */
    public function getWebgainsUsername()
    {
        return $this->webgainsUsername;
    }

    /**
     * Set webgainsPassword.
     *
     * @param string $webgainsPassword
     *
     * @return networkCredentials
     */
    public function setWebgainsPassword($webgainsPassword)
    {
        $this->webgainsPassword = $webgainsPassword;

        return $this;
    }

    /**
     * Get webgainsPassword.
     *
     * @return string
     */
    public function getWebgainsPassword()
    {
        return $this->webgainsPassword;
    }

    /**
     * Set webgainsCampaignId.
     *
     * @param string $webgainsCampaignId
     *
     * @return networkCredentials
     */
    public function setWebgainsCampaignId($webgainsCampaignId)
    {
        $this->webgainsCampaignId = $webgainsCampaignId;

        return $this;
    }

    /**
     * Get webgainsCampaignId.
     *
     * @return string
     */
    public function getWebgainsCampaignId()
    {
        return $this->webgainsCampaignId;
    }

    /**
     * Set webgainsLocation.
     *
     * @param string $webgainsLocation
     *
     * @return networkCredentials
     */
    public function setWebgainsLocation($webgainsLocation)
    {
        $this->webgainsLocation = $webgainsLocation;

        return $this;
    }

    /**
     * Get webgainsLocation.
     *
     * @return string
     */
    public function getWebgainsLocation()
    {
        return $this->webgainsLocation;
    }

    /**
     * Set webgainsUri.
     *
     * @param string $webgainsUri
     *
     * @return networkCredentials
     */
    public function setWebgainsUri($webgainsUri)
    {
        $this->webgainsUri = $webgainsUri;

        return $this;
    }

    /**
     * Get webgainsUri.
     *
     * @return string
     */
    public function getWebgainsUri()
    {
        return $this->webgainsUri;
    }

    /**
     * Set zenoxConnectId.
     *
     * @param string $zenoxConnectId
     *
     * @return networkCredentials
     */
    public function setZenoxConnectId($zenoxConnectId)
    {
        $this->zenoxConnectId = $zenoxConnectId;

        return $this;
    }

    /**
     * Get zenoxConnectId.
     *
     * @return string
     */
    public function getZenoxConnectId()
    {
        return $this->zenoxConnectId;
    }

    /**
     * Set zenoxSecretKey.
     *
     * @param string $zenoxSecretKey
     *
     * @return networkCredentials
     */
    public function setZenoxSecretKey($zenoxSecretKey)
    {
        $this->zenoxSecretKey = $zenoxSecretKey;

        return $this;
    }

    /**
     * Get zenoxSecretKey.
     *
     * @return string
     */
    public function getZenoxSecretKey()
    {
        return $this->zenoxSecretKey;
    }

    /**
     * Set zenoxRegion.
     *
     * @param string $zenoxRegion
     *
     * @return networkCredentials
     */
    public function setZenoxRegion($zenoxRegion)
    {
        $this->zenoxRegion = $zenoxRegion;

        return $this;
    }

    /**
     * Get zenoxRegion.
     *
     * @return string
     */
    public function getZenoxRegion()
    {
        return $this->zenoxRegion;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return networkCredentials
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
     * @return networkCredentials
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
     * Set amazonCredentials.
     *
     * @param string amazonCredentials
     *
     * @return networkCredentials
     */
    public function setAmazonCredentials($amazonCredentials)
    {
        $this->amazonCredentials = $amazonCredentials;

        return $this;
    }

    /**
     * Get amazonCredentials.
     *
     * @return string
     */
    public function getAmazonCredentials()
    {
        return $this->amazonCredentials;
    }

    public function addAmazonCredentials(AmazonCredentials $amazonCredentials)
    {
        $this->amazonCredentials->add($amazonCredentials);
        $amazonCredentials->setNetworkCredentials($this);
    }

    public function removeAmazonCredentials(AmazonCredentials $amazonCredentials)
    {
        $this->amazonCredentials->removeElement($amazonCredentials);
        $amazonCredentials->setNetworkCredentials(null);
    }
}
