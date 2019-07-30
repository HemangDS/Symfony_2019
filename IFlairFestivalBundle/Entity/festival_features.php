<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * festival_features
 *
 * @ORM\Table(name="festival_features")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\festival_featuresRepository")
 */
class festival_features
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
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\features", inversedBy="festival_features")
     * @ORM\JoinColumn(name = "feature_id", referencedColumnName = "id")
     */
    private $featureId;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\festival", inversedBy="festival_features")
     * @ORM\JoinColumn(name = "festival_id", referencedColumnName = "id")
     */
    private $festivalId;

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
     * Set status
     *
     * @param string $status
     *
     * @return festival_features
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
     * Set featureId
     *
     * @param \IFlairFestivalBundle\Entity\features $featureId
     *
     * @return festival_features
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
     * Set festivalId
     *
     * @param \IFlairFestivalBundle\Entity\festival $festivalId
     *
     * @return festival_features
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
}
