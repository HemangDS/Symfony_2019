<?php

namespace iFlair\LetsBonusFrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subscription.
 *
 * @ORM\Table(name="lb_subscription")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusFrontBundle\Entity\SubscriptionRepository")
 */
class Subscription
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
     * @var string
     *
     * @ORM\Column(name="s_email", type="string", length=255)
     */
    private $sEmail;

    /**
     * @var int
     *
     * @ORM\Column(name="s_selligentid", type="integer")
     */
    private $sSelligentid;

    /**
     * @var string
     *
     * @ORM\Column(name="s_status", type="string", length=255)
     */
    private $sStatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\OneToMany(targetEntity="NewsletterSubscriptionUsers", mappedBy="subscriptionId")
     */
    private $usersubscription;

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
     * @return Subscription
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
     * Set sSelligentid.
     *
     * @param int $sSelligentid
     *
     * @return Subscription
     */
    public function setSSelligentid($sSelligentid)
    {
        $this->sSelligentid = $sSelligentid;

        return $this;
    }

    /**
     * Get sSelligentid.
     *
     * @return int
     */
    public function getSSelligentid()
    {
        return $this->sSelligentid;
    }

    /**
     * Set sStatus.
     *
     * @param string $sStatus
     *
     * @return Subscription
     */
    public function setSStatus($sStatus)
    {
        $this->sStatus = $sStatus;

        return $this;
    }

    /**
     * Get sStatus.
     *
     * @return string
     */
    public function getSStatus()
    {
        return $this->sStatus;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Subscription
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
     * Constructor.
     */
    public function __construct()
    {
        $this->usersubscription = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add usersubscription.
     *
     * @param \iFlair\LetsBonusFrontBundle\Entity\NewsletterSubscriptionUsers $usersubscription
     *
     * @return Subscription
     */
    public function addUsersubscription(\iFlair\LetsBonusFrontBundle\Entity\NewsletterSubscriptionUsers $usersubscription)
    {
        $this->usersubscription[] = $usersubscription;

        return $this;
    }

    /**
     * Remove usersubscription.
     *
     * @param \iFlair\LetsBonusFrontBundle\Entity\NewsletterSubscriptionUsers $usersubscription
     */
    public function removeUsersubscription(\iFlair\LetsBonusFrontBundle\Entity\NewsletterSubscriptionUsers $usersubscription)
    {
        $this->usersubscription->removeElement($usersubscription);
    }

    /**
     * Get usersubscription.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsersubscription()
    {
        return $this->usersubscription;
    }
}
