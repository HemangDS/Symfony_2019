<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Newsletter.
 *
 * @ORM\Table(name="lb_newsletter")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\NewsletterRepository")
 */
class Newsletter
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="MailchimpCampaignNewsletterStatus", mappedBy="Newsletter") 
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nname", type="string", length=255)
     */
    private $nname;

    /**
     * @var \iFlair\LetsBonusAdminBundle\Entity\Newslettertemplate
     *
     * @ORM\ManyToOne(targetEntity="iFlair\LetsBonusAdminBundle\Entity\Newslettertemplate", cascade={"persist"}, fetch="LAZY")
     */
    private $templatename;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ndate", type="datetime")
     */
    private $ndate;

    /**
     * @var string
     *
     * @ORM\Column(name="asunto", type="string", length=255)
     */
    private $asunto;

    /**
     * @var \iFlair\LetsBonusAdminBundle\Entity\MailchimpLists
     *
     * @ORM\ManyToOne(targetEntity="iFlair\LetsBonusAdminBundle\Entity\MailchimpLists", cascade={"persist"}, fetch="LAZY")
     */
    private $list;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var \iFlair\LetsBonusAdminBundle\Entity\NewsletterBanner
     *
     * @ORM\ManyToMany(targetEntity="iFlair\LetsBonusAdminBundle\Entity\NewsletterBanner", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinTable(name="lb_newsletter_newsletter_banner",
     *      joinColumns={@ORM\JoinColumn(name="newsletter_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="newsletter_banner_id", referencedColumnName="id")}
     *      )
     */
    private $bannername;

    /**
     * @var string
     *
     * @ORM\Column(name="shopblocktitle", type="string", length=255)
     */
    private $shopblocktitle;

    /**
     * @var int
     *
     * @ORM\Column(name="campaign_id", type="integer", nullable=true)
     */
    private $campaignId;

    /**
     * @var \iFlair\LetsBonusAdminBundle\Entity\shopHistory
     *
     * @ORM\ManyToMany(targetEntity="iFlair\LetsBonusAdminBundle\Entity\shopHistory", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinTable(name="lb_newsletter_shop_history",
     *      joinColumns={@ORM\JoinColumn(name="newsletter_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="shop_history_id", referencedColumnName="id")}
     *      )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="voucherblocktitle", type="string", length=255)
     */
    private $voucherblocktitle;

    /**
     * @var \iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms
     *
     * @ORM\ManyToMany(targetEntity="iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinTable(name="lb_newsletter_voucher_programs",
     *      joinColumns={@ORM\JoinColumn(name="newsletter_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="voucher_programs_id", referencedColumnName="id")}
     *      )
     */
    private $programName;

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
     * @ORM\OneToMany(targetEntity="iFlair\LetsBonusFrontBundle\Entity\NewsletterSubscriptionUsers", mappedBy="newsletterId")
     */
    private $usernewsletter;

    public function __construct()
    {
        $this->variation = new ArrayCollection();
        $this->bannername = new ArrayCollection();
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
      
        /*$this->name = new \Doctrine\Common\Collections\ArrayCollection();*/
    }

    public function __toString()
    {
        return (string) $this->id;
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
     * Set asunto.
     *
     * @param string $asunto
     *
     * @return Newsletter
     */
    public function setAsunto($asunto)
    {
        $this->asunto = $asunto;

        return $this;
    }

    /**
     * Get asunto.
     *
     * @return string
     */
    public function getAsunto()
    {
        return $this->asunto;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Newsletter
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
     * @return Newsletter
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
     * Set nname.
     *
     * @param string $nname
     *
     * @return Newsletter
     */
    public function setNname($nname)
    {
        $this->nname = $nname;

        return $this;
    }

    /**
     * Get nname.
     *
     * @return string
     */
    public function getNname()
    {
        return $this->nname;
    }

    /**
     * Add bannername.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\NewsletterBanner $bannername
     *
     * @return Newsletter
     */
    public function addBannername(\iFlair\LetsBonusAdminBundle\Entity\NewsletterBanner $bannername)
    {
        $this->bannername[] = $bannername;

        return $this;
    }

    /**
     * Remove bannername.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\NewsletterBanner $bannername
     */
    public function removeBannername(\iFlair\LetsBonusAdminBundle\Entity\NewsletterBanner $bannername)
    {
        $this->bannername->removeElement($bannername);
    }

    /**
     * Get bannername.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBannername()
    {
        return $this->bannername;
    }

    /**
     * Add title.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\shopHistory $title
     *
     * @return Newsletter
     */
    public function addTitle(\iFlair\LetsBonusAdminBundle\Entity\shopHistory $title)
    {
        $this->title[] = $title;

        return $this;
    }

    /**
     * Remove title.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\shopHistory $title
     */
    public function removeTitle(\iFlair\LetsBonusAdminBundle\Entity\shopHistory $title)
    {
        $this->title->removeElement($title);
    }

    /**
     * Get title.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add programName.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms $programName
     *
     * @return Newsletter
     */
    public function addProgramName(\iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms $programName)
    {
        $this->programName[] = $programName;

        return $this;
    }

    /**
     * Remove programName.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms $programName
     */
    public function removeProgramName(\iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms $programName)
    {
        $this->programName->removeElement($programName);
    }

    /**
     * Get programName.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProgramName()
    {
        return $this->programName;
    }

    /**
     * Set templatename.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Newslettertemplate $templatename
     *
     * @return Newsletter
     */
    public function setTemplatename(\iFlair\LetsBonusAdminBundle\Entity\Newslettertemplate $templatename = null)
    {
        $this->templatename = $templatename;

        return $this;
    }

    /**
     * Get templatename.
     *
     * @return \iFlair\LetsBonusAdminBundle\Entity\Newslettertemplate
     */
    public function getTemplatename()
    {
        return $this->templatename;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return Newsletter
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set ndate.
     *
     * @param \DateTime $ndate
     *
     * @return Newsletter
     */
    public function setNdate($ndate)
    {
        $this->ndate = $ndate;

        return $this;
    }

    /**
     * Get ndate.
     *
     * @return \DateTime
     */
    public function getNdate()
    {
        return $this->ndate;
    }

    /**
     * Set shopblocktitle.
     *
     * @param string $shopblocktitle
     *
     * @return Newsletter
     */
    public function setShopblocktitle($shopblocktitle)
    {
        $this->shopblocktitle = $shopblocktitle;

        return $this;
    }

    /**
     * Get shopblocktitle.
     *
     * @return string
     */
    public function getShopblocktitle()
    {
        return $this->shopblocktitle;
    }

    /**
     * Set voucherblocktitle.
     *
     * @param string $voucherblocktitle
     *
     * @return Newsletter
     */
    public function setVoucherblocktitle($voucherblocktitle)
    {
        $this->voucherblocktitle = $voucherblocktitle;

        return $this;
    }

    /**
     * Get voucherblocktitle.
     *
     * @return string
     */
    public function getVoucherblocktitle()
    {
        return $this->voucherblocktitle;
    }

    /**
     * Add usernewsletter.
     *
     * @param \iFlair\LetsBonusFrontBundle\Entity\NewsletterSubscriptionUsers $usernewsletter
     *
     * @return Newsletter
     */
    public function addUsernewsletter(\iFlair\LetsBonusFrontBundle\Entity\NewsletterSubscriptionUsers $usernewsletter)
    {
        $this->usernewsletter[] = $usernewsletter;

        return $this;
    }

    /**
     * Remove usernewsletter.
     *
     * @param \iFlair\LetsBonusFrontBundle\Entity\NewsletterSubscriptionUsers $usernewsletter
     */
    public function removeUsernewsletter(\iFlair\LetsBonusFrontBundle\Entity\NewsletterSubscriptionUsers $usernewsletter)
    {
        $this->usernewsletter->removeElement($usernewsletter);
    }

    /**
     * Get usernewsletter.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsernewsletter()
    {
        return $this->usernewsletter;
    }

    /**
     * Set list_id.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\MailchimpLists $list_id
     *
     * @return Newsletter
     */
    public function setList(\iFlair\LetsBonusAdminBundle\Entity\MailchimpLists $list = null)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * Get list_id.
     *
     * @return \iFlair\LetsBonusAdminBundle\Entity\MailchimpLists
     */
    public function getList()
    {
        return $this->list;
    }
}
