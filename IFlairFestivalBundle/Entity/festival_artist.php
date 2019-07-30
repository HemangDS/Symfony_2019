<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * festival_artist
 *
 * @ORM\Table(name="festival_artist")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\festival_artistRepository")
 */
class festival_artist
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
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\artist", inversedBy="festival_artist")
     * @ORM\JoinColumn(name = "artist_id", referencedColumnName = "id")
     */
    private $artistId;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\festival_dates", inversedBy="festival_artist")
     * @ORM\JoinColumn(name = "festival_dates_id", referencedColumnName = "id")
     */
    private $festivalDatesId;

    /**
     * @var string
     *
     * @ORM\Column(name="confirmed", type="string", length=255, columnDefinition="enum('0', '1')")
     */
    private $confirmed;


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
     * Set confirmed
     *
     * @param string $confirmed
     *
     * @return festival_artist
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * Get confirmed
     *
     * @return string
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * Set festivalId
     *
     * @param \IFlairFestivalBundle\Entity\festival $festivalId
     *
     * @return festival_artist
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
     * Set artistId
     *
     * @param \IFlairFestivalBundle\Entity\artist $artistId
     *
     * @return festival_artist
     */
    public function setArtistId(\IFlairFestivalBundle\Entity\artist $artistId = null)
    {
        $this->artistId = $artistId;

        return $this;
    }

    /**
     * Get artistId
     *
     * @return \IFlairFestivalBundle\Entity\artist
     */
    public function getArtistId()
    {
        return $this->artistId;
    }

    /**
     * Set festivalDatesId
     *
     * @param \IFlairFestivalBundle\Entity\festival_dates $festivalDatesId
     *
     * @return festival_artist
     */
    public function setFestivalDatesId(\IFlairFestivalBundle\Entity\festival_dates $festivalDatesId = null)
    {
        $this->festivalDatesId = $festivalDatesId;

        return $this;
    }

    /**
     * Get festivalDatesId
     *
     * @return \IFlairFestivalBundle\Entity\festival_dates
     */
    public function getFestivalDatesId()
    {
        return $this->festivalDatesId;
    }
}
