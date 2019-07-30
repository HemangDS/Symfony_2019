<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdvType.
 *
 * @ORM\Table(name="lb_advertisement_type")
 * @ORM\Entity
 */
class AdvertisementType
{
    const ADTYPEHEADER = 'Header';
    const ADTYPEMIDDLECONTENT = 'Middle Content';
    const ADTYPEFOOTER = 'Footer';
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
     * @ORM\Column(name="adv_type_name", type="string", length=255)
     */
    private $advTypeName;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    public function __toString()
    {
        return strval($this->id);
    }

    /**
     * Set advTypeName.
     *
     * @param string $advTypeName
     *
     * @return AdvType
     */
    public function setAdvTypeName($advTypeName)
    {
        $this->advTypeName = $advTypeName;

        return $this;
    }

    /**
     * Get advTypeName.
     *
     * @return string
     */
    public function getAdvTypeName()
    {
        return $this->advTypeName;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Network
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
     * @return Network
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
