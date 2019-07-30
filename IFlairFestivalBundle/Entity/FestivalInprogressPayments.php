<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FestivalInprogressPayments
 *
 * @ORM\Table(name="festival_inprogress_payments")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\FestivalInprogressPaymentsRepository")
 */
class FestivalInprogressPayments
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
     * @ORM\ManyToOne(targetEntity="IFlairSoapBundle\Entity\Payments", inversedBy="festival_payment")
     * @ORM\JoinColumn(name = "payment_id", referencedColumnName = "id")
     */
    private $paymentId;

    /**
     * @ORM\ManyToOne(targetEntity="IFlairFestivalBundle\Entity\FestivalInprogress", inversedBy="festival_inprogress_payments")
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
     * Set paymentId
     *
     * @param \IFlairSoapBundle\Entity\Payments $paymentId
     *
     * @return FestivalInprogressPayments
     */
    public function setPaymentId(\IFlairSoapBundle\Entity\Payments $paymentId = null)
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    /**
     * Get paymentId
     *
     * @return \IFlairSoapBundle\Entity\Payments
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * Set festivalInprogressId
     *
     * @param \IFlairFestivalBundle\Entity\FestivalInprogress $festivalInprogressId
     *
     * @return FestivalInprogressPayments
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
