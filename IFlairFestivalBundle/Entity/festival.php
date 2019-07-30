<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * festival
 *
 * @ORM\Table(name="festival")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\festivalRepository")
 */
class festival
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\festival_location", inversedBy="festival")
     * @ORM\JoinColumn(name = "festival_location_id", referencedColumnName = "id")
     */
    private $festivalLocationId;

    /**
     * @var string
     *
     * @ORM\Column(name="held_since", type="string", length=255, nullable=true)
     */
    private $heldSince;

    /**
     * @var string
     *
     * @ORM\Column(name="stages", type="string", length=255, nullable=true)
     */
    private $stages;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="festival")
     * @ORM\JoinColumn(name = "user_admin", referencedColumnName = "id")
     */
    private $user_admin;

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
     * Set title
     *
     * @param string $title
     *
     * @return festival
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return festival
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set heldSince
     *
     * @param string $heldSince
     *
     * @return festival
     */
    public function setHeldSince($heldSince)
    {
        $this->heldSince = $heldSince;

        return $this;
    }

    /**
     * Get heldSince
     *
     * @return string
     */
    public function getHeldSince()
    {
        return $this->heldSince;
    }

    /**
     * Set stages
     *
     * @param string $stages
     *
     * @return festival
     */
    public function setStages($stages)
    {
        $this->stages = $stages;

        return $this;
    }

    /**
     * Get stages
     *
     * @return string
     */
    public function getStages()
    {
        return $this->stages;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return festival
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set festivalLocationId
     *
     * @param \IFlairFestivalBundle\Entity\festival_location $festivalLocationId
     *
     * @return festival
     */
    public function setFestivalLocationId(\IFlairFestivalBundle\Entity\festival_location $festivalLocationId = null)
    {
        $this->festivalLocationId = $festivalLocationId;

        return $this;
    }

    /**
     * Get festivalLocationId
     *
     * @return \IFlairFestivalBundle\Entity\festival_location
     */
    public function getFestivalLocationId()
    {
        return $this->festivalLocationId;
    }



    /**
     * Set userAdmin
     *
     * @param \AppBundle\Entity\User $userAdmin
     *
     * @return festival
     */
    public function setUserAdmin(\AppBundle\Entity\User $userAdmin = null)
    {
        $this->user_admin = $userAdmin;

        return $this;
    }

    /**
     * Get userAdmin
     *
     * @return \AppBundle\Entity\User
     */
    public function getUserAdmin()
    {
        return $this->user_admin;
    }
}
