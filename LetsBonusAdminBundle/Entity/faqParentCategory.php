<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * faqParentCategory.
 *
 * @ORM\Table(name="lb_faq_parent_category")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\faqParentCategoryRepository")
 */
class faqParentCategory
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

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
     * @ORM\OneToMany(targetEntity="faqQuestion", mappedBy="faqParentCategory")
     */
    private $faqQuestion;

    public function __construct()
    {
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

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return faqParentCategory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set status.
     *
     * @param bool $status
     *
     * @return faqParentCategory
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

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return faqParentCategory
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
     * @return faqParentCategory
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

    /**
     * Add faqQuestion.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\faqQuestion $faqQuestion
     *
     * @return faqParentCategory
     */
    public function addFaqQuestion(\iFlair\LetsBonusAdminBundle\Entity\faqQuestion $faqQuestion)
    {
        $this->faqQuestion[] = $faqQuestion;

        return $this;
    }

    /**
     * Remove faqQuestion.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\faqQuestion $faqQuestion
     */
    public function removeFaqQuestion(\iFlair\LetsBonusAdminBundle\Entity\faqQuestion $faqQuestion)
    {
        $this->faqQuestion->removeElement($faqQuestion);
    }

    /**
     * Get faqQuestion.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFaqQuestion()
    {
        return $this->faqQuestion;
    }
}
