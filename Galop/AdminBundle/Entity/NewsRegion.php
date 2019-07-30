<?php

namespace Galop\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NewsRegion
 *
 * @ORM\Table(name="news_region")
 * @ORM\Entity(repositoryClass="Galop\AdminBundle\Repository\NewsRegionRepository")
 */
class NewsRegion
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
     * @ORM\Column(name="region", type="string", length=255)
     */
    private $region;

    /**
     * @ORM\OneToMany(targetEntity="News", mappedBy="region", cascade={"remove"})
     */
    private $news;

    public function __toString(){
        return $this->region;    
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
     * Set region.
     *
     * @param string $region
     *
     * @return NewsRegion
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region.
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }
}
