<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FestivalInfoEditConfirmation
 *
 * @ORM\Table(name="festival_info_edit_confirmation")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\FestivalInfoEditConfirmationRepository")
 */
class FestivalInfoEditConfirmation
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
     * @ORM\Column(name="festival_info", type="text", nullable=true, length=2000)
     */
    private $festivalInfo;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, columnDefinition="enum('0', '1')")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="festival_info_edit_confirmation")
     * @ORM\JoinColumn(name = "user_id", referencedColumnName = "id")
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\festival", inversedBy="festival_info_edit_confirmation")
     * @ORM\JoinColumn(name = "festival_id", referencedColumnName = "id")
     */
    private $festivalId;


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
     * Set status
     *
     * @param string $status
     *
     * @return FestivalInfoEditConfirmation
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set userId
     *
     * @param \AppBundle\Entity\User $userId
     *
     * @return FestivalInfoEditConfirmation
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

    /**
     * Set festivalId
     *
     * @param \IFlairFestivalBundle\Entity\festival $festivalId
     *
     * @return FestivalInfoEditConfirmation
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
     * Set festivalInfo
     *
     * @param string $festivalInfo
     *
     * @return FestivalInfoEditConfirmation
     */
    public function setFestivalInfo($festivalInfo)
    {
        $this->festivalInfo = $festivalInfo;

        return $this;
    }

    /**
     * Get festivalInfo
     *
     * @return string
     */
    public function getFestivalInfo()
    {
        return $this->festivalInfo;
    }
}
