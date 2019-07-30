<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MailchimpCampaign.
 *
 * @ORM\Table(name="lb_mailchimp_campaign")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\MailchimpCampaignRepository")
 */
class MailchimpCampaign
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="MailchimpCampaignNewsletterStatus", mappedBy="MailchimpCampaign") 
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="campaign_name", type="string", length=255)
     */
    private $campaign_name;

    /**
     * @var string
     *
     * @ORM\Column(name="campaign_id", type="string", length=255)
     */
    private $campaign_id;

    /**
     * @var string
     *
     * @ORM\Column(name="campaign_status", type="string", length=255)
     */
    private $campaign_status;

    public function __toString()
    {
        return strval($this->id);
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
     * Set id.
     *
     * @param int $id
     *
     * @return MailchimpCampaign
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set campaign_name.
     *
     * @param string $campaign_name
     *
     * @return MailchimpCampaign
     */
    public function setCampaignName($campaign_name)
    {
        $this->campaign_name = $campaign_name;

        return $this;
    }

    /**
     * Get campaign_name.
     *
     * @return string
     */
    public function getCampaignName()
    {
        return $this->campaign_name;
    }

    /**
     * Set campaign_id.
     *
     * @param string $campaign_id
     *
     * @return MailchimpCampaign
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
     * Set campaign_status.
     *
     * @param string $campaign_status
     *
     * @return MailchimpCampaign
     */
    public function setCampaignStatus($campaign_status)
    {
        $this->campaign_status = $campaign_status;

        return $this;
    }

    /**
     * Get campaign_status.
     *
     * @return string
     */
    public function getCampaignStatus()
    {
        return $this->campaign_status;
    }

}
