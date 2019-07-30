<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FestivalInprogressMultipleImageUpload
 *
 * @ORM\Table(name="festival_inprogress_multiple_image_upload")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\FestivalInprogressMultipleImageUploadRepository")
 */
class FestivalInprogressMultipleImageUpload
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
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\festival", inversedBy="festival_currency")
     * @ORM\JoinColumn(name = "festival_id", referencedColumnName = "id")
     */
    private $festivalId;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="contribution_add_festival")
     * @ORM\JoinColumn(name = "user_id", referencedColumnName = "id")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="image_path", type="text", nullable=true, length=2000)
     */
    private $imagePath;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_approved", type="boolean")
     */
    private $isApproved;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set isApproved
     *
     * @param boolean $isApproved
     *
     * @return FestivalInprogressMultipleImageUpload
     */
    public function setIsApproved($isApproved)
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    /**
     * Get isApproved
     *
     * @return bool
     */
    public function getIsApproved()
    {
        return $this->isApproved;
    }

    /**
     * Set imagePath
     *
     * @param string $imagePath
     *
     * @return FestivalInprogressMultipleImageUpload
     */
    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    /**
     * Get imagePath
     *
     * @return string
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     * Set festivalId
     *
     * @param \IFlairFestivalBundle\Entity\festival $festivalId
     *
     * @return FestivalInprogressMultipleImageUpload
     */
    public function setFestivalId(\IFlairFestivalBundle\Entity\festival $festivalId = null)
    {
        $this->festivalId = $festivalId;

        return $this;
    }

    /**
     * Get festivalId
     *
     * @return \IFlairFestivalBundle\Entity\festival
     */
    public function getFestivalId()
    {
        return $this->festivalId;
    }

    /**
     * Set userId
     *
     * @param \AppBundle\Entity\User $userId
     *
     * @return FestivalInprogressMultipleImageUpload
     */
    public function setUserId(\AppBundle\Entity\User $userId = null)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return \AppBundle\Entity\User
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
