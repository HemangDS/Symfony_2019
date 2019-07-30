<?php

namespace Galop\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NewsDescipline
 *
 * @ORM\Table(name="news_descipline")
 * @ORM\Entity(repositoryClass="Galop\AdminBundle\Repository\NewsDesciplineRepository")
 */
class NewsDescipline
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
     * @ORM\Column(name="descipline", type="string", length=255)
     */
    private $descipline;

    
    /**
     * @ORM\OneToMany(targetEntity="News", mappedBy="descipline", cascade={"remove"})
     */
    private $news;

    public function __toString(){
        return $this->descipline;    
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

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    
    /**
     * Set descipline.
     *
     * @param string $descipline
     *
     * @return NewsDescipline
     */
    public function setDescipline($descipline)
    {
        $this->descipline = $descipline;

        return $this;
    }

    /**
     * Get descipline.
     *
     * @return string
     */
    public function getDescipline()
    {
        return $this->descipline;
    }
}
