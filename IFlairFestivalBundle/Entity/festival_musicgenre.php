<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * festival_musicgenre
 *
 * @ORM\Table(name="festival_musicgenre")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\festival_musicgenreRepository")
 */
class festival_musicgenre
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
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\festival", inversedBy="festival_musicgenre")
     * @ORM\JoinColumn(name = "festival_id", referencedColumnName = "id")
     */
    private $festivalId;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairSoapBundle\Entity\Musicgenre", inversedBy="festival_musicgenre")
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
     * Set festivalId
     *
     * @param \IFlairFestivalBundle\Entity\festival $festivalId
     *
     * @return festival_musicgenre
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
     * Set musicGenreId
     *
     * @param \IFlairSoapBundle\Entity\Musicgenre $musicGenreId
     *
     * @return festival_musicgenre
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
}
