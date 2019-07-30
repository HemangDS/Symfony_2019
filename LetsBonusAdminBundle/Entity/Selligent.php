<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Selligent.
 *
 * @ORM\Table(name="lb_selligent")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\SelligentRepository")
 */
class Selligent
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
     * @var int
     *
     * @ORM\Column(name="selligent_id", type="integer")
     */
    private $selligentId;

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

    public function __construct()
    {
        $this->variation = new ArrayCollection();
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
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

    public function __toString()
    {
        return (string) $this->selligentId;
    }

    /**
     * Set selligentId.
     *
     * @param int $selligentId
     *
     * @return Selligent
     */
    public function setSelligentId($selligentId)
    {
        $this->selligentId = $selligentId;

        return $this;
    }

    /**
     * Get selligentId.
     *
     * @return int
     */
    public function getSelligentId()
    {
        return $this->selligentId;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Selligent
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
     * @return Selligent
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
}
