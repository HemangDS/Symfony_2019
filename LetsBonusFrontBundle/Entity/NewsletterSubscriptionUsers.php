<?php

namespace iFlair\LetsBonusFrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NewsletterSubscriptionUsers.
 *
 * @ORM\Table(name="lb_subscription_newsletter")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusFrontBundle\Entity\NewsletterSubscriptionUsersRepository")
 */
class NewsletterSubscriptionUsers
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
     * @ORM\ManyToOne(targetEntity="Subscription", cascade={"persist"}, inversedBy="usersubscription")    
     * @ORM\JoinColumn(name="subscription_id", referencedColumnName="id")
     */
    private $subscriptionId;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="iFlair\LetsBonusAdminBundle\Entity\Newsletter", cascade={"persist"}, inversedBy="usernewsletter")    
     * @ORM\JoinColumn(name="newsletter_id", referencedColumnName="id")
     */
    private $newsletterId;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

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
     * Set status.
     *
     * @param int $status
     *
     * @return NewsletterSubscriptionUsers
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
     * Set newsletterId.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Newsletter $newsletterId
     *
     * @return NewsletterSubscriptionUsers
     */
    public function setNewsletterId(\iFlair\LetsBonusAdminBundle\Entity\Newsletter $newsletterId = null)
    {
        $this->newsletterId = $newsletterId;

        return $this;
    }

    /**
     * Get newsletterId.
     *
     * @return \iFlair\LetsBonusAdminBundle\Entity\Newsletter
     */
    public function getNewsletterId()
    {
        return $this->newsletterId;
    }

    /**
     * Set subscriptionId.
     *
     * @param \iFlair\LetsBonusFrontBundle\Entity\Subscription $subscriptionId
     *
     * @return NewsletterSubscriptionUsers
     */
    public function setSubscriptionId(\iFlair\LetsBonusFrontBundle\Entity\Subscription $subscriptionId = null)
    {
        $this->subscriptionId = $subscriptionId;

        return $this;
    }

    /**
     * Get subscriptionId.
     *
     * @return \iFlair\LetsBonusFrontBundle\Entity\Subscription
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }
}
