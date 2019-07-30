<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use FOS\UserBundle\Entity\User as AbstractedUser;
use iFlair\LetsBonusFrontBundle\Entity\Review;

/**
 * FrontUser.
 *
 * @ORM\Table(name="lb_front_user",indexes={@Index(name="srch_by_token",columns={"confirmation_token","login_type"})})
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\FrontUserRepository")
 */
class FrontUser extends AbstractedUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255, nullable=true)
     */
    private $alias;
    /**
     * @var int
     *
     * @ORM\Column(name="is_shoppiday", type="integer")
     */
    private $isShoppiday;

     /**
     * @var int
     *
     * @ORM\Column(name="api_flag", type="integer")
     */
    private $apiFlag;
    /**
     * @var int
     *
     * @ORM\Column(name="company_id", type="integer", nullable=true)
     */
    private $companyId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="user_create_date", type="datetime", nullable=true)
     */
    private $userCreateDate;

    /**
     * @var string
     *
     * @ORM\Column(name="user_type", type="string", length=255, nullable=true)
     */
    private $userType;

    /**
     * @var int
     *
     * @ORM\Column(name="user_gender", type="integer")
     */
    private $userGender;

    /**
     * @var int
     *
     * @ORM\Column(name="is_subscribed", type="integer", nullable=true)
     */
    private $isSubscribed;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $image;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="user_birth_date", type="datetime", nullable=true)
     */
    private $userBirthDate;

    /**
     * @var int
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="login_type", type="string", length=255, nullable=true)
     */
    private $loginType;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook_id", type="string", length=255, nullable=true)
     */
    private $facebookId;

    /**
     * @var string
     *
     * @ORM\Column(name="google_id", type="string", length=255, nullable=true)
     */
    private $googleId;

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
     * @ORM\OneToMany(targetEntity="AddtoFev", mappedBy="shopHistoryId")
     */
    private $addtofav;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AddtoFev", mappedBy="userId")
     */
    private $addtofavUser;

    /**
     * @ORM\OneToOne(targetEntity="UserPaymentDetail", mappedBy="userid")
     */
    private $userPaymentDetail;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="iFlair\LetsBonusFrontBundle\Entity\Review", mappedBy="userId")
     */
    private $review;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=15, nullable=true)
     */
    private $phone;

    public function __toString()
    {
        return (string) $this->id;
    }

    public function __construct()
    {
        parent::__construct();
        $this->addtofav = new ArrayCollection();
        $this->addtofavUser = new ArrayCollection();
        $this->review = new ArrayCollection();
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    /* Getter to show full name in admin form field start */
    public function getUserFullName()
    {
        return $this->getName().' '.$this->getSurname();
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return FrontUser
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get surname.
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set surname.
     *
     * @param string $surname
     *
     * @return FrontUser
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    public function getAddToFav()
    {
        return $this->addtofav;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return FrontUser
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set alias.
     *
     * @param string $alias
     *
     * @return FrontUser
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get companyId.
     *
     * @return int
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Set companyId.
     *
     * @param int $companyId
     *
     * @return FrontUser
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;

        return $this;
    }


    /**
     * Set userCreateDate.
     *
     * @param \DateTime $userCreateDate
     *
     * @return FrontUser
     */
    public function setUserCreateDate($userCreateDate)
    {
        $this->userCreateDate = $userCreateDate;

        return $this;
    }

    /**
     * Get userCreateDate.
     *
     * @return \DateTime
     */
    public function getUserCreateDate()
    {
        return $this->userCreateDate;
    }

    /**
     * Get userType.
     *
     * @return string
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * Set userType.
     *
     * @param string $userType
     *
     * @return FrontUser
     */
    public function setUserType($userType)
    {
        $this->userType = $userType;

        return $this;
    }

    /**
     * Get userGender.
     *
     * @return int
     */
    public function getUserGender()
    {
        return (int) $this->userGender;
    }

    /**
     * Set userGender.
     *
     * @param int $userGender
     *
     * @return FrontUser
     */
    public function setUserGender($userGender)
    {
        $this->userGender = $userGender;

        return $this;
    }

    /**
     * Get isSubscribed.
     *
     * @return int
     */
    public function getIsSubscribed()
    {
        return $this->isSubscribed;
    }

    /**
     * Set isSubscribed.
     *
     * @param int $isSubscribed
     *
     * @return FrontUser
     */
    public function setIsSubscribed($isSubscribed)
    {
        $this->isSubscribed = $isSubscribed;

        return $this;
    }

    /**
     * Get image.
     *
     * @return Media
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set image.
     *
     * @param Media $image
     *
     * @return FrontUser
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get userBirthDate.
     *
     * @return \DateTime
     */
    public function getUserBirthDate()
    {
        return $this->userBirthDate;
    }

    /**
     * Set userBirthDate.
     *
     * @param \DateTime $userBirthDate
     *
     * @return FrontUser
     */
    public function setUserBirthDate($userBirthDate)
    {
        $this->userBirthDate = $userBirthDate;

        return $this;
    }

    /**
     * Get city.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set city.
     *
     * @param string $city
     *
     * @return FrontUser
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get loginType.
     *
     * @return string
     */
    public function getLoginType()
    {
        return $this->loginType;
    }

    /**
     * Set loginType.
     *
     * @param string $loginType
     *
     * @return FrontUser
     */
    public function setLoginType($loginType)
    {
        $this->loginType = $loginType;

        return $this;
    }

    /**
     * Get facebookId.
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * Set facebookId.
     *
     * @param string $facebookId
     *
     * @return FrontUser
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Get googleId.
     *
     * @return int
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * Set googleId.
     *
     * @param int $googleId
     *
     * @return FrontUser
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;

        return $this;
    }
    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return FrontUser
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
     * @return FrontUser
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
     * Add addtofav.
     *
     * @param AddtoFev $addtofav
     *
     * @return FrontUser
     */
    public function addAddtofav(AddtoFev $addtofav)
    {
        $this->addtofav[] = $addtofav;

        return $this;
    }

    /**
     * Remove addtofav.
     *
     * @param AddtoFev $addtofav
     */
    public function removeAddtofav(AddtoFev $addtofav)
    {
        $this->addtofav->removeElement($addtofav);
    }

    /**
     * Get userPaymentDetail.
     *
     * @return UserPaymentDetail
     */
    public function getUserPaymentDetail()
    {
        return $this->userPaymentDetail;
    }

    /**
     * @param UserPaymentDetail $userPaymentDetail
     */
    public function setUserPaymentDetail($userPaymentDetail)
    {
        $this->userPaymentDetail = $userPaymentDetail;
    }

    /**
     * Add addtofavUser.
     *
     * @param AddtoFev $addtofavUser
     *
     * @return FrontUser
     */
    public function addAddtofavUser(AddtoFev $addtofavUser)
    {
        $this->addtofavUser[] = $addtofavUser;

        return $this;
    }

    /**
     * Remove addtofavUser.
     *
     * @param AddtoFev $addtofavUser
     */
    public function removeAddtofavUser(AddtoFev $addtofavUser)
    {
        $this->addtofavUser->removeElement($addtofavUser);
    }

    /**
     * Get addtofavUser.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddtofavUser()
    {
        return $this->addtofavUser;
    }

    /**
     * Add review.
     *
     * @param Review $review
     *
     * @return FrontUser
     */
    public function addReview(Review $review)
    {
        $this->review[] = $review;

        return $this;
    }

    /**
     * Remove review.
     *
     * @param Review $review
     */
    public function removeReview(Review $review)
    {
        $this->review->removeElement($review);
    }

    /**
     * Get review.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Method to determine if the user is from the migration
     *
     * @return bool
     */
    public function isToImport()
    {
        return $this->getIsShoppiday() === 0 && $this->getApiFlag() === 0;
    }

    /**
     * Get isShoppiday.
     *
     * @return int
     */
    public function getIsShoppiday()
    {
        return (int) $this->isShoppiday;
    }

    /**
     * Set isShoppiday.
     *
     * @param int $isShoppiday
     *
     * @return FrontUser
     */
    public function setIsShoppiday($isShoppiday)
    {
        $this->isShoppiday = $isShoppiday;

        return $this;
    }

    /**
     * Get apiFlag.
     *
     * @return int
     */
    public function getApiFlag()
    {
        return (int) $this->apiFlag;
    }

    /**
     * Set apiFlag.
     *
     * @param int $apiFlag
     *
     * @return FrontUser
     */
    public function setApiFlag($apiFlag)
    {
        $this->apiFlag = $apiFlag;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = preg_replace('![-\s()]!', '', $phone);
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used by the equals method and the username.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(
            [
                $this->modified,
                $this->created,
                $this->addtofav,
                $this->addtofavUser,
                $this->review,
                $this->userPaymentDetail,
                $this->name,
                $this->surname,
                $this->alias,
                $this->isShoppiday,
                $this->apiFlag,
                $this->companyId,
                $this->userType,
                $this->userGender,
                $this->isSubscribed,
                $this->userBirthDate,
                $this->city,
                $this->loginType,
                $this->facebookId,
                $this->googleId,
                $this->phone,
                $this->password,
                $this->salt,
                $this->usernameCanonical,
                $this->email,
                $this->username,
                $this->expired,
                $this->locked,
                $this->credentialsExpired,
                $this->enabled,
                $this->id,
            ]
        );
    }

    /**
     * Unserializes the user.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        list(
            $this->modified,
            $this->created,
            $this->addtofav,
            $this->addtofavUser,
            $this->review,
            $this->userPaymentDetail,
            $this->name,
            $this->surname,
            $this->alias,
            $this->isShoppiday,
            $this->apiFlag,
            $this->companyId,
            $this->userType,
            $this->userGender,
            $this->isSubscribed,
            $this->userBirthDate,
            $this->city,
            $this->loginType,
            $this->facebookId,
            $this->googleId,
            $this->phone,
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->email,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id
            ) = $data;
    }
}
