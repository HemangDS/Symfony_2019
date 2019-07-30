<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FestivalInprogressFeatures
 *
 * @ORM\Table(name="festival_inprogress_features")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\FestivalInprogressFeaturesRepository")
 */
class FestivalInprogressFeatures
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
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\features", inversedBy="contribution_add_festival")
     * @ORM\JoinColumn(name = "feature_id", referencedColumnName = "id")
     */
    private $featureId;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\FestivalInprogress", inversedBy="festival_inprogress_features")
     * @ORM\JoinColumn(name = "festival_inprogress_id", referencedColumnName = "id")
     */
    private $festivalInprogressId;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, columnDefinition="enum('0', '1')")
     */
    private $status;

    public function __toString()
    {
        return strval($this->id);
    }

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
     * @return FestivalInprogressFeatures
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
     * Set festivalInprogressId
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogress $festivalInprogressId
     *
     * @return FestivalInprogressFeatures
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
     * Set status
     *
     * @param string $status
     *
     * @return FestivalInprogressFeatures
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
}
