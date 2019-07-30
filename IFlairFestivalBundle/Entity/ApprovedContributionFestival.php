<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApprovedContributionFestival
 *
 * @ORM\Table(name="approved_contribution_festival")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\ApprovedContributionFestivalRepository")
 */
class ApprovedContributionFestival
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
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\festival", inversedBy="festival_artist")
     * @ORM\JoinColumn(name = "festival_id", referencedColumnName = "id")
     */
    private $festivalId;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\ContributionAdddFestival", inversedBy="contribution_addd_festival")
     * @ORM\JoinColumn(name = "contribution_id", referencedColumnName = "id")
     */
    private $contributionId;


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
     * Set festivalId
     *
     * @param \IFlairFestivalBundle\Entity\festival $festivalId
     *
     * @return ApprovedContributionFestival
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
     * Set contributionId
     *
     * @param \IFlairFestivalBundle\Entity\ContributionAdddFestival $contributionId
     *
     * @return ApprovedContributionFestival
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
