<?php

namespace Galop\AdminBundle\Entity;

use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="Galop\AdminBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @ORM\OneToMany(targetEntity="News", mappedBy="createdByUser", cascade={"remove"})
     */
    private $news;

    /**
     * @ORM\OneToMany(targetEntity="News", mappedBy="updatedByUser", cascade={"remove"})
     */
    private $news1;

    /**
     * @ORM\OneToMany(targetEntity="Advertisement", mappedBy="userid", cascade={"remove"})
     */
    private $advertisement;

    /**
     * @ORM\OneToMany(targetEntity="Advertisement", mappedBy="updatedByUser", cascade={"remove"})
     */
    private $advertisementone;

     /**
     * @ORM\OneToMany(targetEntity="Events", mappedBy="createdByUser", cascade={"remove"})
     */
    private $eventscreate;

     /**
     * @ORM\OneToMany(targetEntity="Events", mappedBy="updatedByUser", cascade={"remove"})
     */
    private $eventsupdate;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}
