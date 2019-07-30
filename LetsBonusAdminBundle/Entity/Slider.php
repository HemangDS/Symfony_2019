<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Slider.
 *
 * @ORM\Table(name="lb_slider")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\SliderRepository")
 */
class Slider
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
     * @var \Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="enabled", type="integer")
     */
    private $enabled;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var int
     *
     * @ORM\Column(name="show_in_front", type="integer")
     */
    private $show_in_front;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime")
     */
    private $modified;
    /**
     * @var string
     *
     * @ORM\Column(name="start_date", type="string", length=255)
     */
    private $start_date;
    /**
     * @var string
     *
     * @ORM\Column(name="end_date", type="string", length=255)
     */
    private $end_date;


    /**
     * @var string
     *
     * @ORM\Column(name="slider_area", type="string")
     */
    private $slider_area;

    public function __construct()
    {
        $this->variation = new ArrayCollection();
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set image.
     *
     * @param string $image
     *
     * @return Slider
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Slider
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Slider
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set enabled.
     *
     * @param string $enabled
     *
     * @return Slider
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return string
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set show_in_front.
     *
     * @param string $show_in_front
     *
     * @return Slider
     */
    public function setShowInFront($show_in_front)
    {
        $this->show_in_front = $show_in_front;

        return $this;
    }

    /**
     * Get show_in_front.
     *
     * @return string
     */
    public function getShowInFront()
    {
        return $this->show_in_front;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Slider
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified.
     *
     * @param \DateTime $modified
     *
     * @return Slider
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified.
     *
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set start_date.
     *
     * @param \string $start_date
     *
     * @return Slider
     */
    public function setStartDate($start_date)
    {
        $this->start_date = $start_date;

        return $this;
    }

    /**
     * Get start_date.
     *
     * @return \string
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set end_date.
     *
     * @param \string $end_date
     *
     * @return Slider
     */
    public function setEndDate($end_date)
    {
        $this->end_date = $end_date;

        return $this;
    }

    /**
     * Get end_date.
     *
     * @return \string
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return Slider
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }


    /**
     * Set slider_area .
     *
     * @param string $slider_area
     *
     * @return Slider
     */
    public function setSliderArea($slider_area)
    {
        $this->slider_area = $slider_area;

        return $this;
    }

    /**
     * Get slider_area.
     *
     * @return string
     */
    public function getSliderArea()
    {
        return $this->slider_area;
    }

}
