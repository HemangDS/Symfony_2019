<?php

namespace Galop\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tag
 *
 * @ORM\Table(name="tag")
 * @ORM\Entity(repositoryClass="Galop\AdminBundle\Repository\TagRepository")
 */
class Tag
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
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;


    /**
     * @var array|null
     * @ORM\ManyToMany(targetEntity="TagGroup", inversedBy="tag")
     * @ORM\JoinTable(name="tag_taggroup",
     *      joinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     *      )
     */
    private $group;

    /**
     * @ORM\ManyToMany(targetEntity="News", mappedBy="tags", cascade={"persist","remove"})
     */
    private $news;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;
    
    public function __toString(){
        return $this->title;    
    }

    public function __construct()
    {
        $this->group = new ArrayCollection();
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
     * @param string|null $title
     *
     * @return Tag
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set group.
     *
     * @param int|null $group
     *
     * @return Tag
     */
    public function setGroup(ArrayCollection $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group.
     *
     * @return int|null
     */
    public function getGroup()
    {
        return $this->group;
    }


    public function addGroup(TagGroup $group)
    {
        $this->group[] = $group;

        return $this;
    }

    public function removeGroup(TagGroup $group)
    {
        $this->group->removeElement($group);
    }


    /**
     * Get news.
     *
     * @return int|null
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * Set news.
     *
     * @param int|null $news
     *
     * @return News
     */
    public function setNews(News $news = null)
    {
        $this->news = $news;

        return $this;
    }

    public function addNews(News $news)
    {
        $this->news[] = $news;

        return $this;
    }

    public function removeNews(Tag $news)
    {
        $this->news->removeElement($news);
    }

    /**
     * Set status.
     *
     * @param bool $status
     *
     * @return Tag
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }
}
