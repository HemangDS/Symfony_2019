<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MailchimpUserListStatus.
 *
 * @ORM\Table(name="lb_mailchimp_user_list_status")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\MailchimpUserListStatusRepository")

 */
class MailchimpUserListStatus
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
     * @ORM\ManyToOne(targetEntity="MailchimpSubscription", inversedBy="id") 
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false) 
     */
    private $user_id;

    /**
     * @ORM\ManyToOne(targetEntity="MailchimpLists", inversedBy="id") 
     * @ORM\JoinColumn(name="list_id", referencedColumnName="id", nullable=false) 
     */
    private $list_id;

     /**
     * @ORM\ManyToOne(targetEntity="MailchimpSegmentListNewsletter", inversedBy="id") 
     * @ORM\JoinColumn(name="segment_id", referencedColumnName="id", nullable=false) 
     */
    private $segment_id;

     /**
     * @var string
     *
     * @ORM\Column(name="user_mailchimp_status", type="string", length=255)
     */
    private $user_mailchimp_status;

    /**
     * @var string
     *
     * @ORM\Column(name="user_registered", type="string", length=255)
     */
    private $user_registered;

    /**
     * @var string
     *
     * @ORM\Column(name="user_segment_status", type="string", length=255)
     */
    private $user_segment_status;
  
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
     * @return MailchimpUserListStatus
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set user_id.
     *
     * @param string $user_id
     *
     * @return MailchimpUserListStatus
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Get user_id.
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set list_id.
     *
     * @param string $list_id
     *
     * @return MailchimpUserListStatus
     */
    public function setListId($list_id)
    {
        $this->list_id = $list_id;

        return $this;
    }

    /**
     * Get list_id.
     *
     * @return string
     */
    public function getListId()
    {
        return $this->list_id;
    }

     /**
     * Set user_mailchimp_status.
     *
     * @param string $user_mailchimp_status
     *
     * @return MailchimpUserListStatus
     */
    public function setUserMailchimpStatus($user_mailchimp_status)
    {
        $this->user_mailchimp_status = $user_mailchimp_status;

        return $this;
    }

    /**
     * Get user_mailchimp_status.
     *
     * @return string
     */
    public function getUserMailchimpStatus()
    {
        return $this->user_mailchimp_status;
    }

    /**
     * Set segment_id.
     *
     * @param string $segment_id
     *
     * @return MailchimpUserListStatus
     */
    public function setSegmentId($segment_id)
    {
        $this->segment_id = $segment_id;

        return $this;
    }

    /**
     * Get segment_id.
     *
     * @return string
     */
    public function getSegmentId()
    {
        return $this->segment_id;
    }

     /**
     * Set user_registered.
     *
     * @param string $user_registered
     *
     * @return MailchimpUserListStatus
     */
    public function setUserRegistered($user_registered)
    {
        $this->user_registered = $user_registered;

        return $this;
    }

    /**
     * Get user_registered.
     *
     * @return string
     */
    public function getUserRegistered()
    {
        return $this->user_registered;
    }

    /**
     * Set user_segment_status.
     *
     * @param string $user_segment_status
     *
     * @return MailchimpUserListStatus
     */
    public function setUserSegmentStatus($user_segment_status)
    {
        $this->user_segment_status = $user_segment_status;

        return $this;
    }

    /**
     * Get user_segment_status.
     *
     * @return string
     */
    public function getUserSegmentStatus()
    {
        return $this->user_segment_status;
    }
}
