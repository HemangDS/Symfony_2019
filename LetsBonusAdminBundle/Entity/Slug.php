<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Slug.
 *
 * @ORM\Table(name="lb_slug")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\SlugRepository")
 */
class Slug
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
     * @ORM\Column(name="categoryType", type="integer")
     */
    private $categoryType;

    /**
     * @var string
     *
     * @ORM\Column(name="slugName", type="string", length=255)
     */
    private $slugName;

    /**
     * @var int
     *
     * @ORM\Column(name="categoryId", type="integer")
     */
    private $categoryId;

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
     * Set categoryType.
     *
     * @param int $categoryType
     *
     * @return Slug
     */
    public function setCategoryType($categoryType)
    {
        $this->categoryType = $categoryType;

        return $this;
    }

    /**
     * Get categoryType.
     *
     * @return int
     */
    public function getCategoryType()
    {
        return $this->categoryType;
    }

    /**
     * Set slugName.
     *
     * @param string $slugName
     *
     * @return Slug
     */
    public function setSlugName($slugName)
    {
        $this->slugName = $slugName;

        return $this;
    }

    /**
     * Get slugName.
     *
     * @return string
     */
    public function getSlugName()
    {
        return $this->slugName;
    }

    /**
     * Set categoryId.
     *
     * @param int $categoryId
     *
     * @return Slug
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId.
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }
}
