<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MailchimpLists.
 *
 * @ORM\Table(name="lb_mailchimp_lists")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\MailchimpListsRepository")
 */
class MailchimpLists
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="MailchimpUserListStatus", mappedBy="MailchimpLists") 
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="list_name", type="string", length=255)
     */
    private $list_name;

    /**
     * @var string
     *
     * @ORM\Column(name="list_id", type="string", length=255)
     */
    private $list_id;

    
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
     * @return MailchimpLists
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set list_name.
     *
     * @param string $list_name
     *
     * @return MailchimpLists
     */
    public function setListName($list_name)
    {
        $this->list_name = $list_name;

        return $this;
    }

    /**
     * Get list_name.
     *
     * @return string
     */
    public function getListName()
    {
        return $this->list_name;
    }

    /**
     * Set list_id.
     *
     * @param string $list_id
     *
     * @return MailchimpLists
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

}
