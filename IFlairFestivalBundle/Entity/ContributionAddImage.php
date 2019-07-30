<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContributionAddImage
 *
 * @ORM\Table(name="contribution_add_image")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\ContributionAddImageRepository")
 */
class ContributionAddImage
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
     * @var string
     *
     * @ORM\Column(name="image_path", type="string", length=255)
     */
    private $imagePath;


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
     * Set imagePath
     *
     * @param string $imagePath
     *
     * @return ContributionAddImage
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
     * Set contributionId
     *
     * @param \IFlairFestivalBundle\Entity\ContributionAdddFestival $contributionId
     *
     * @return ContributionAddImage
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
