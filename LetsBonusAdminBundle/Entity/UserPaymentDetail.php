<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserPaymentDetail.
 *
 * @ORM\Table(name="lb_user_payment_details")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\UserPaymentDetailRepository")
 * @ORM\HasLifecycleCallbacks
 */
class UserPaymentDetail
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
     * @ORM\Column(name="owner_name", type="string", length=255, nullable=true)
     */
    private $ownername;

    /**
     * @var string
     *
     * @ORM\Column(name="account_number", type="string", length=255, nullable=true)
     */
    private $accountnumber;

    /**
     * @var string
     *
     * @ORM\Column(name="swift_code_bic", type="string", length=255, nullable=true)
     */
    private $swiftcodebic;

    /**
     * @ORM\OneToOne(targetEntity="FrontUser", inversedBy="userPaymentDetail", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userid;

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

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
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
     * Get ownername.
     *
     * @return string
     */
    public function getOwnername()
    {
        return $this->ownername;
    }

    /**
     * Set ownername.
     *
     * @param string $ownername
     *
     * @return UserPaymentDetail
     */
    public function setOwnername($ownername)
    {
        $this->ownername = $ownername;

        return $this;
    }

    /**
     * Get accountnumber.
     *
     * @return string
     */
    public function getAccountnumber()
    {
        return $this->accountnumber;
    }

    /**
     * Get accountnumber.
     *
     * @return string
     */
    public function getHideAccountnumber()
    {
        return substr($this->accountnumber, 0, 4).str_repeat('*', 16).substr($this->accountnumber, -4);
    }
    /**
     * Set accountnumber.
     *
     * @param int $accountnumber
     *
     * @return UserPaymentDetail
     */
    public function setAccountnumber($accountnumber)
    {
        $this->accountnumber = $accountnumber;

        return $this;
    }

    /**
     * Get swiftcodebic.
     *
     * @return string
     */
    public function getSwiftcodebic()
    {
        return $this->swiftcodebic;
    }

    /**
     * Set swiftcodebic.
     *
     * @param int $swiftcodebic
     *
     * @return UserPaymentDetail
     */
    public function setSwiftcodebic($swiftcodebic)
    {
        $this->swiftcodebic = $swiftcodebic;

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
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return UserPaymentDetail
     */
    public function setCreated($created)
    {
        $this->created = $created;

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
     * Set modified.
     *
     * @param \DateTime $modified
     *
     * @return UserPaymentDetail
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    public function getUserid()
    {
        return $this->userid;
    }

    public function setUserid(FrontUser $userid)
    {
        $this->userid = $userid;
    }


    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function doStuffOnPrePersist()
    {
        $this->modified = new \DateTime();
    }
}
