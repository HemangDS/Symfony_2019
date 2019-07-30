<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FestivalInprogressMusicgenre
 *
 * @ORM\Table(name="festival_inprogress_musicgenre")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\FestivalInprogressMusicgenreRepository")
 */
class FestivalInprogressMusicgenre
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
     * @ORM\ManyToOne(targetEntity="IFlairSoapBundle\Entity\Musicgenre", inversedBy="contribution_add_festival")
     * @ORM\JoinColumn(name = "musicgenre_id", referencedColumnName = "id")
     */
    private $musicgenreId;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\FestivalInprogress", inversedBy="festival_inprogress_musicgenre")
     * @ORM\JoinColumn(name = "festival_inprogress_id", referencedColumnName = "id")
     */
    private $festivalInprogressId;

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
     * Set musicgenreId
     *
     * @param \IFlairSoapBundle\Entity\Musicgenre $musicgenreId
     *
     * @return FestivalInprogressMusicgenre
     */
    public function setMusicgenreId(\IFlairSoapBundle\Entity\Musicgenre $musicgenreId = null)
    {
        $this->musicgenreId = $musicgenreId;

        return $this;
    }

    /**
     * Get musicgenreId
     *
     * @return \IFlairSoapBundle\Entity\Musicgenre
     */
    public function getMusicgenreId()
    {
        return $this->musicgenreId;
    }

    /**
     * Set festivalInprogressId
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogress $festivalInprogressId
     *
     * @return FestivalInprogressMusicgenre
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
}
