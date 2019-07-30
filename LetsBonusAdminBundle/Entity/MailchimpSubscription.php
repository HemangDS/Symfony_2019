<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MailchimpSubscription.
 *
 * @ORM\Table(name="lb_mailchimp_subscription")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\MailchimpSubscriptionRepository")
 */
class MailchimpSubscription
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="MailchimpUserListStatus", mappedBy="MailchimpSubscription") 
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="s_email", type="string", length=255)
     */
    private $sEmail;
    /**
      @var string
     *
     * @ORM\Column(name="s_status", type="string", length=255)
     */
   // private $sStatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

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
     * Set sEmail.
     *
     * @param string $sEmail
     *
     * @return MailchimpSubscription
     */
    public function setSEmail($sEmail)
    {
        $this->sEmail = $sEmail;

        return $this;
    }

    /**
     * Get sEmail.
     *
     * @return string
     */
    public function getSEmail()
    {
        return $this->sEmail;
    }

    /**
     * Set sStatus.
     *
      @param string $sStatus
     *
      @return MailchimpSubscription
     */
   /* public function setSStatus($sStatus)
    {
        $this->sStatus = $sStatus;

        return $this;
    }*/

    /**
     * Get sStatus.
     *
      @return string
     */
   /* public function getSStatus()
    {
        return $this->sStatus;
    }*/


    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return MailchimpSubscription
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

}
