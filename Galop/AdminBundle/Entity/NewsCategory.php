<?php

namespace Galop\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NewsCategory
 *
 * @ORM\Table(name="news_category")
 * @ORM\Entity(repositoryClass="Galop\AdminBundle\Repository\NewsCategoryRepository")
 */
class NewsCategory
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
     * @ORM\Column(name="category", type="string", length=255)
     */
    private $category;

    
    /**
     * @ORM\OneToMany(targetEntity="News", mappedBy="category", cascade={"remove"})
     */
    private $news;

    public function __toString(){
        return $this->category;    
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

    /**
     * Set category.
     *
     * @param string $category
     *
     * @return NewsCategory
     */
    public function setCategory($category)
    { 
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }
}
