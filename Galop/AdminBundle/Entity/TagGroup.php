<?php

namespace Galop\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TagGroup
 *
 * @ORM\Table(name="tag_group")
 * @ORM\Entity(repositoryClass="Galop\AdminBundle\Repository\TagGroupRepository")
 */
class TagGroup
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;
    
    /**
     * @ORM\ManyToMany(targetEntity="Tag", mappedBy="group", cascade={"persist","remove"})
     */
    private $tag;

    public function __toString(){
        return $this->title;
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
     * Set title.
     *
     * @param string $title
     *
     * @return TagGroup
     */
    public function setTitle($title)
    { 
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * Get tag.
     *
     * @return int|null
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set tag.
     *
     * @param int|null $tag
     *
     * @return TagGroup
     */
    public function setTag(Tag $tag = null)
    {
        $this->tag = $tag;

        return $this;
    }

    public function addTag(Tag $tag)
    {
        $this->tag[] = $tag;

        return $this;
    }

    public function removeTag(Tag $tag)
    {
        $this->tag->removeElement($tag);
    }
}
