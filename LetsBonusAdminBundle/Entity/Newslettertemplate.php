<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Newslettertemplate.
 *
 * @ORM\Table(name="lb_newslettertemplates")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\NewslettertemplateRepository")
 */
class Newslettertemplate
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
     * @ORM\Column(name="templatename", type="string", length=255)
     */
    private $templatename;

    public function __toString()
    {
        return (string) $this->templatename;
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
     * Set templatename.
     *
     * @param string $templatename
     *
     * @return Newslettertemplate
     */
    public function setTemplatename($templatename)
    {
        $this->templatename = $templatename;

        return $this;
    }

    /**
     * Get templatename.
     *
     * @return string
     */
    public function getTemplatename()
    {
        return $this->templatename;
    }
}
