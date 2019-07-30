<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FestivalInprogressRatings
 *
 * @ORM\Table(name="festival_inprogress_ratings")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\FestivalInprogressRatingsRepository")
 */
class FestivalInprogressRatings
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
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\FestivalInprogress", inversedBy="festival_inprogress_currency")
     * @ORM\JoinColumn(name = "festivalInprogressId", referencedColumnName = "id")
     */
    private $festivalInprogressId;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\festival_rating_type", inversedBy="contribution_add_festival")
     * @ORM\JoinColumn(name = "rating_id", referencedColumnName = "id")
     */
    private $ratingId;

    /**
     * @var string
     *
     * @ORM\Column(name="userRatings", type="string", length=255)
     */
    private $userRatings;


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
     * Set userRatings
     *
     * @param string $userRatings
     *
     * @return FestivalInprogressRatings
     */
    public function setUserRatings($userRatings)
    {
        $this->userRatings = $userRatings;

        return $this;
    }

    /**
     * Get userRatings
     *
     * @return string
     */
    public function getUserRatings()
    {
        return $this->userRatings;
    }

    /**
     * Set festivalInprogressId
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogress $festivalInprogressId
     *
     * @return FestivalInprogressRatings
     */
    public function setFestivalInprogressId(\IFlairFestivalBundle\Entity\FestivalInprogress $festivalInprogressId = null)
    {
        $this->festivalInprogressId = $festivalInprogressId;

        return $this;
    }

    /**
     * Get festivalInprogressId
     *
     * @return \IFlairFestivalBundle\Entity\FestivalInprogress
     */
    public function getFestivalInprogressId()
    {
        return $this->festivalInprogressId;
    }

    /**
     * Set ratingId
     *
     * @param \IFlairFestivalBundle\Entity\festival_rating_type $ratingId
     *
     * @return FestivalInprogressRatings
     */
    public function setRatingId(\IFlairFestivalBundle\Entity\festival_rating_type $ratingId = null)
    {
        $this->ratingId = $ratingId;

        return $this;
    }

    /**
     * Get ratingId
     *
     * @return \IFlairFestivalBundle\Entity\festival_rating_type
     */
    public function getRatingId()
    {
        return $this->ratingId;
    }
}
