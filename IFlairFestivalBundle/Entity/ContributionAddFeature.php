<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContributionAddFeature
 *
 * @ORM\Table(name="contribution_add_feature")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\ContributionAddFeatureRepository")
 */
class ContributionAddFeature
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
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\features", inversedBy="contribution_add_festival")
     * @ORM\JoinColumn(name = "feature_id", referencedColumnName = "id")
     */
    private $featureId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, columnDefinition="enum('0', '1')")
     */
    private $status;

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
     * Set featureId
     *
     * @param \IFlairFestivalBundle\Entity\features $featureId
     *
     * @return ContributionAddFeature
     */
    public function setFeatureId(\IFlairFestivalBundle\Entity\features $featureId = null)
    {
        $this->featureId = $featureId;

        return $this;
    }

    /**
     * Get featureId
     *
     * @return \IFlairFestivalBundle\Entity\features
     */
    public function getFeatureId()
    {
        return $this->featureId;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return ContributionAddFeature
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
     * Set contributionId
     *
     * @param \IFlairFestivalBundle\Entity\ContributionAdddFestival $contributionId
     *
     * @return ContributionAddFeature
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
