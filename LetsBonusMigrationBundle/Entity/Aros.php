<?php

namespace iFlair\LetsBonusMigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Aros.
 *
 * @ORM\Table(name="aros", indexes={@ORM\Index(name="idx_aros_lft_rght", columns={"lft", "rght"}), @ORM\Index(name="idx_aros_alias", columns={"alias"}), @ORM\Index(name="model", columns={"model"}), @ORM\Index(name="foreign_key", columns={"foreign_key"}), @ORM\Index(name="lft", columns={"lft"}), @ORM\Index(name="rght", columns={"rght"})})
 * @ORM\Entity
 */
class Aros
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parentId;

    /**
     * @var string
     *
     * @ORM\Column(name="model", type="string", length=255, nullable=true)
     */
    private $model;

    /**
     * @var int
     *
     * @ORM\Column(name="foreign_key", type="integer", nullable=true)
     */
    private $foreignKey;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255, nullable=true)
     */
    private $alias;

    /**
     * @var int
     *
     * @ORM\Column(name="lft", type="integer", nullable=true)
     */
    private $lft;

    /**
     * @var int
     *
     * @ORM\Column(name="rght", type="integer", nullable=true)
     */
    private $rght;

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
     * Set parentId.
     *
     * @param int $parentId
     *
     * @return Aros
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId.
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set model.
     *
     * @param string $model
     *
     * @return Aros
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set foreignKey.
     *
     * @param int $foreignKey
     *
     * @return Aros
     */
    public function setForeignKey($foreignKey)
    {
        $this->foreignKey = $foreignKey;

        return $this;
    }

    /**
     * Get foreignKey.
     *
     * @return int
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * Set alias.
     *
     * @param string $alias
     *
     * @return Aros
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set lft.
     *
     * @param int $lft
     *
     * @return Aros
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft.
     *
     * @return int
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set rght.
     *
     * @param int $rght
     *
     * @return Aros
     */
    public function setRght($rght)
    {
        $this->rght = $rght;

        return $this;
    }

    /**
     * Get rght.
     *
     * @return int
     */
    public function getRght()
    {
        return $this->rght;
    }
}
