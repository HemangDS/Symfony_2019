<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContributionAddRating
 *
 * @ORM\Table(name="contribution_add_rating")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\ContributionAddRatingRepository")
 */
class ContributionAddRating
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
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\ContributionAdddFestival", inversedBy="contribution_addd_festival")
     * @ORM\JoinColumn(name = "contribution_id", referencedColumnName = "id")
     */
    private $contributionId;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\festival_rating_type", inversedBy="contribution_add_festival")
     * @ORM\JoinColumn(name = "rating_id", referencedColumnName = "id")
     */
    private $ratingId;

    /**
     * @var string
     *
     * @ORM\Column(name="user_ratings", type="string", length=255)
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
     * Set ratingId
     *
     * @param \IFlairFestivalBundle\Entity\festival_rating_type $ratingId
     *
     * @return ContributionAddRating
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

    /**
     * Set userRatings
     *
     * @param string $userRatings
     *
     * @return ContributionAddRating
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
     * Set contributionId
     *
     * @param \IFlairFestivalBundle\Entity\ContributionAdddFestival $contributionId
     *
     * @return ContributionAddRating
     */
    public function setContributionId(\IFlairFestivalBundle\Entity\ContributionAdddFestival $contributionId = null)
    {
        $this->contributionId = $contributionId;

        return $this;
    }

    /**
     * Get contributionId
     *
     * @return \IFlairFestivalBundle\Entity\ContributionAdddFestival
     */
    public function getContributionId()
    {
        return $this->contributionId;
    }
}
