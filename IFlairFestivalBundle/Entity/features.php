<?php

namespace IFlairFestivalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * features
 *
 * @ORM\Table(name="features")
 * @ORM\Entity(repositoryClass="IFlairFestivalBundle\Repository\featuresRepository")
 */
class features
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
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="useas", type="string", length=255, columnDefinition="enum('nightclub/bar', 'festival')")
     */
    private $useas;

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
     * Set type
     *
     * @param string $type
     *
     * @return features
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
     * Set name
     *
     * @param string $name
     *
     * @return features
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set useas
     *
     * @param string $useas
     *
     * @return features
     */
    public function setUseas($useas)
    {
        $this->useas = $useas;

        return $this;
    }

    /**
     * Get useas
     *
     * @return string
     */
    public function getUseas()
    {
        return $this->useas;
    }
}
