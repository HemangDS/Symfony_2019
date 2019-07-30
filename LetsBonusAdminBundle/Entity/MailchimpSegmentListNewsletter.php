<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MailchimpSegmentListNewsletter.
 *
 * @ORM\Table(name="lb_mailchimp_segment_list_newsletter")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\MailchimpSegmentListNewsletterRepository")

 */
class MailchimpSegmentListNewsletter
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
     * @var integer
     *
     * @ORM\Column(name="segment_id", type="integer")
     */
    private $segment_id;

     /**
     * @var string
     *
     * @ORM\Column(name="segment_name", type="string", length=255)
     */
    private $segment_name;

     /**
     * @var \iFlair\LetsBonusAdminBundle\Entity\MailchimpLists
     *
     * @ORM\ManyToOne(targetEntity="iFlair\LetsBonusAdminBundle\Entity\MailchimpLists", cascade={"persist"}, fetch="LAZY")
     */
    private $list;

    /**
     * @var \iFlair\LetsBonusAdminBundle\Entity\Newsletter
     *
     * @ORM\ManyToOne(targetEntity="iFlair\LetsBonusAdminBundle\Entity\Newsletter", cascade={"persist"}, fetch="LAZY")
     */
    private $newsletter;


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
     * @return MailchimpSegmentListNewsletter
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set segment_id.
     *
     * @param integer $segment_id
     *
     * @return MailchimpSegmentListNewsletter
     */
    public function setSegmentId($segment_id)
    {
        $this->segment_id = $segment_id;

        return $this;
    }

    /**
     * Get segment_id.
     *
     * @return integer
     */
    public function getSegmentId()
    {
        return $this->segment_id;
    }

    /**
     * Set segment_name.
     *
     * @param string $segment_name
     *
     * @return MailchimpSegmentListNewsletter
     */
    public function setSegmentName($segment_name)
    {
        $this->segment_name = $segment_name;

        return $this;
    }

    /**
     * Get segment_name.
     *
     * @return string
     */
    public function getSegmentName()
    {
        return $this->segment_name;
    }

    /**
     * Set list_id.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\MailchimpLists $list_id
     *
     * @return MailchimpSegmentListNewsletter
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

     /**
     * Set newsletter.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\Newsletter $newsletter
     *
     * @return MailchimpSegmentListNewsletter
     */
    public function setNewsletter(\iFlair\LetsBonusAdminBundle\Entity\Newsletter $newsletter = null)
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /**
     * Get newsletter.
     *
     * @return \iFlair\LetsBonusAdminBundle\Entity\Newsletter
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

}
