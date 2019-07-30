<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Festival_inprogress_status
 *
 * @ORM\Table(name="festival_inprogress_status")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\Festival_inprogress_statusRepository")
 */
class Festival_inprogress_status
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
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\FestivalInprogress", inversedBy="Festival_inprogress_status")
     * @ORM\JoinColumn(name = "festival_inprogress_id", referencedColumnName = "id")
     */
    private $festivalInprogressId;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairSoapBundle\Entity\ContributionStatus", inversedBy="Festival_inprogress_status")
     * @ORM\JoinColumn(name = "status_id", referencedColumnName = "id")
     */
    private $statusId;


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
     * Set festivalInprogressId
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogress $festivalInprogressId
     *
     * @return Festival_inprogress_status
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
     * Set statusId
     *
     * @param \IFlairSoapBundle\Entity\ContributionStatus $statusId
     *
     * @return Festival_inprogress_status
     */
    public function setStatusId(\IFlairSoapBundle\Entity\ContributionStatus $statusId = null)
    {
        $this->statusId = $statusId;

        return $this;
    }

    /**
     * Get statusId
     *
     * @return \IFlairSoapBundle\Entity\ContributionStatus
     */
    public function getStatusId()
    {
        return $this->statusId;
    }
}
