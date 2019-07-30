<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MailchimpCampaignNewsletterStatus.
 *
 * @ORM\Table(name="lb_mailchimp_campaign_newsletter_status")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\MailchimpCampaignNewsletterStatusRepository")

 */
class MailchimpCampaignNewsletterStatus
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
     * @ORM\ManyToOne(targetEntity="MailchimpCampaign", inversedBy="id") 
     * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id", nullable=false) 
     */
    private $campaign_id;

    /**
     * @ORM\ManyToOne(targetEntity="Newsletter", inversedBy="id") 
     * @ORM\JoinColumn(name="newsletter_id", referencedColumnName="id", nullable=false) 
     */
    private $newsletter_id;

     /**
     * @var string
     *
     * @ORM\Column(name="campaign_newsletter_status", type="string", length=255)
     */
    private $campaign_newsletter_status;

  
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
     * Set id.
     *
     * @param int $id
     *
     * @return MailchimpCampaignNewsletterStatus
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set campaign_id.
     *
     * @param string $campaign_id
     *
     * @return MailchimpCampaignNewsletterStatus
     */
    public function setCampaignId($campaign_id)
    {
        $this->campaign_id = $campaign_id;

        return $this;
    }

    /**
     * Get campaign_id.
     *
     * @return string
     */
    public function getCampaignId()
    {
        return $this->campaign_id;
    }

    /**
     * Set newsletter_id.
     *
     * @param string $newsletter_id
     *
     * @return MailchimpCampaignNewsletterStatus
     */
    public function setNewsletterId($newsletter_id)
    {
        $this->newsletter_id = $newsletter_id;

        return $this;
    }

    /**
     * Get newsletter_id.
     *
     * @return string
     */
    public function getNewsletterId()
    {
        return $this->newsletter_id;
    }

     /**
     * Set campaign_newsletter_status.
     *
     * @param string $campaign_newsletter_status
     *
     * @return MailchimpCampaignNewsletterStatus
     */
    public function setCampaignNewsletterStatus($campaign_newsletter_status)
    {
        $this->campaign_newsletter_status = $campaign_newsletter_status;

        return $this;
    }

    /**
     * Get campaign_newsletter_status.
     *
     * @return string
     */
    public function getCampaignNewsletterStatus()
    {
        return $this->campaign_newsletter_status;
    }

}
