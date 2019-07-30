<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Settings.
 *
 * @ORM\Table(name="lb_settings")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\SettingsRepository")
 */
class Settings
{
    const NO = 0;
    const YES = 1;
    const CATEGORYPLACEHOLDER = 'category_placeholder'; //Must exist in the database
    const PREV_DOWNLOADED_SEPA_RECORDS = 'prev_downloaded_sepa_records'; //Must exist in the database
    const CATEGORYTOPBANNER = 'category_top_banner';
    const CASHBACKTOPBANNER = 'cashback_top_banner';
    const CUPONESTOPBANNER = 'cupones_top_banner';
    const TIENDASTOPBANNER = 'tiendas_top_banner';
    const COLLECTIONTOPBANNER = 'collection_top_banner';

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
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    private $image;

    /**
     * @var int
     *           
     * @ORM\ManyToOne(targetEntity="Companies", inversedBy="settings")	 
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=true)
     */
    private $companies;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="bannerdescription", type="text")
     */
    private $bannerdescription;

    /**
     * @var string
     *
     * @ORM\Column(name="bannertitle", type="string", length=255)
     */
    private $bannertitle;


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
     * @return Settings
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
     * Set value.
     *
     * @param string $value
     *
     * @return Settings
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set company.
     *
     * @param int $company
     *
     * @return Settings
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company.
     *
     * @return int
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Settings
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set status.
     *
     * @param int $status
     *
     * @return Settings
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
     * Set companies.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Companies $companies
     *
     * @return Settings
     */
    public function setCompanies(\iFlair\LetsBonusAdminBundle\Entity\Companies $companies = null)
    {
        $this->companies = $companies;

        return $this;
    }

    /**
     * Get companies.
     *
     * @return \iFlair\LetsBonusAdminBundle\Entity\Companies
     */
    public function getCompanies()
    {
        return $this->companies;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return Settings
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
     * Set image.
     *
     * @param string $image
     *
     * @return Settings
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return Settings
     */
    public function getImage()
    {
        return $this->image;
    }

        /**
     * Set bannerdescription.
     *
     * @param string $bannerdescription
     *
     * @return Settings
     */
    public function setBannerDescription($bannerdescription)
    {
        $this->bannerdescription = $bannerdescription;

        return $this;
    }

    /**
     * Get bannerdescription.
     *
     * @return string
     */
    public function getBannerDescription()
    {
        return $this->bannerdescription;
    }

     /**
     * Set bannertitle.
     *
     * @param string $bannertitle
     *
     * @return Settings
     */
    public function setBannerTitle($bannertitle)
    {
        $this->bannertitle = $bannertitle;

        return $this;
    }

    /**
     * Get bannertitle.
     *
     * @return string
     */
    public function getBannerTitle()
    {
        return $this->bannertitle;
    }

}
