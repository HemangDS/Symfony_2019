<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * faqQuestion.
 *
 * @ORM\Table(name="lb_faq_question")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\faqQuestionRepository")
 */
class faqQuestion
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
     * @ORM\Column(name="question", type="string", length=255)
     */
    private $question;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @var text
     *
     * @ORM\Column(name="answer", type="text")
     */
    private $answer;

    /**
     * @ORM\ManyToOne(targetEntity="faqParentCategory", inversedBy="faqQuestion",  cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="faq_parent_category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $faqParentCategory;

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

    public function __toString()
    {
        return strval($this->id);
    }

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
     * Set question.
     *
     * @param string $question
     *
     * @return faqQuestion
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question.
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set answer.
     *
     * @param string $answer
     *
     * @return faqUserQuestion
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer.
     *
     * @return bool
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set status.
     *
     * @param bool $status
     *
     * @return faqQuestion
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
     * @return faqQuestion
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
     * @return faqQuestion
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
     * Set faqParentCategory.
     *
     * @param \iFlair\LetsBonusAdminBundle\Entity\faqParentCategory $faqParentCategory
     *
     * @return faqQuestion
     */
    public function setFaqParentCategory(\iFlair\LetsBonusAdminBundle\Entity\faqParentCategory $faqParentCategory = null)
    {
        $this->faqParentCategory = $faqParentCategory;

        return $this;
    }

    /**
     * Get faqParentCategory.
     *
     * @return \iFlair\LetsBonusAdminBundle\Entity\faqParentCategory
     */
    public function getFaqParentCategory()
    {
        return $this->faqParentCategory;
    }
}
