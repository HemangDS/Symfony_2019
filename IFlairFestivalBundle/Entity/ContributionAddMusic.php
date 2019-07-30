<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContributionAddMusic
 *
 * @ORM\Table(name="contribution_add_music")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\ContributionAddMusicRepository")
 */
class ContributionAddMusic
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
     * @ORM\ManyToOne(targetEntity="IFlairSoapBundle\Entity\Musicgenre", inversedBy="contribution_add_festival")
     * @ORM\JoinColumn(name = "music_genre_id", referencedColumnName = "id")
     */
    private $musicGenreId;


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
     * Set musicGenreId
     *
     * @param \IFlairSoapBundle\Entity\Musicgenre $musicGenreId
     *
     * @return ContributionAddMusic
     */
    public function setMusicGenreId(\IFlairSoapBundle\Entity\Musicgenre $musicGenreId = null)
    {
        $this->musicGenreId = $musicGenreId;

        return $this;
    }

    /**
     * Get musicGenreId
     *
     * @return \IFlairSoapBundle\Entity\Musicgenre
     */
    public function getMusicGenreId()
    {
        return $this->musicGenreId;
    }

    /**
     * Set contributionId
     *
     * @param \IFlairFestivalBundle\Entity\ContributionAdddFestival $contributionId
     *
     * @return ContributionAddMusic
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
