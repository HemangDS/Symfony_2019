<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * festival_rating_type
 *
 * @ORM\Table(name="festival_rating_type")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\festival_rating_typeRepository")
 */
class festival_rating_type
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
     * @ORM\Column(name="type_name", type="string", length=255)
     */
    private $typeName;

    /**
     * @var string
     *
     * @ORM\Column(name="main_type", type="string", length=255)
     */
    private $mainType;

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
     * Set typeName
     *
     * @param string $typeName
     *
     * @return festival_rating_type
     */
    public function setTypeName($typeName)
    {
        $this->typeName = $typeName;

        return $this;
    }

    /**
     * Get typeName
     *
     * @return string
     */
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * Set mainType
     *
     * @param string $mainType
     *
     * @return festival_rating_type
     */
    public function setMainType($mainType)
    {
        $this->mainType = $mainType;

        return $this;
    }

    /**
     * Get mainType
     *
     * @return string
     */
    public function getMainType()
    {
        return $this->mainType;
    }
}
