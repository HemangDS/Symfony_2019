<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NewsletterCalendar.
 *
 * @ORM\Table(name="lb_newsletter_calender")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\NewsletterCalendarRepository")
 */
class NewsletterCalendar
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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
