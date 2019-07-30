<?php

namespace iFlair\LetsBonusMigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArosAcos.
 *
 * @ORM\Table(name="aros_acos", uniqueConstraints={@ORM\UniqueConstraint(name="ARO_ACO_KEY", columns={"aro_id", "aco_id"})}, indexes={@ORM\Index(name="idx_aco_id", columns={"aco_id"})})
 * @ORM\Entity
 */
class ArosAcos
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
     * @ORM\Column(name="aro_id", type="integer", nullable=true)
     */
    private $aroId;

    /**
     * @var int
     *
     * @ORM\Column(name="aco_id", type="integer", nullable=true)
     */
    private $acoId;

    /**
     * @var string
     *
     * @ORM\Column(name="_create", type="string", length=2, nullable=true)
     */
    private $create = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="_read", type="string", length=2, nullable=true)
     */
    private $read = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="_update", type="string", length=2, nullable=true)
     */
    private $update = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="_delete", type="string", length=2, nullable=true)
     */
    private $delete = '0';

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
     * Set aroId.
     *
     * @param int $aroId
     *
     * @return ArosAcos
     */
    public function setAroId($aroId)
    {
        $this->aroId = $aroId;

        return $this;
    }

    /**
     * Get aroId.
     *
     * @return int
     */
    public function getAroId()
    {
        return $this->aroId;
    }

    /**
     * Set acoId.
     *
     * @param int $acoId
     *
     * @return ArosAcos
     */
    public function setAcoId($acoId)
    {
        $this->acoId = $acoId;

        return $this;
    }

    /**
     * Get acoId.
     *
     * @return int
     */
    public function getAcoId()
    {
        return $this->acoId;
    }

    /**
     * Set create.
     *
     * @param string $create
     *
     * @return ArosAcos
     */
    public function setCreate($create)
    {
        $this->create = $create;

        return $this;
    }

    /**
     * Get create.
     *
     * @return string
     */
    public function getCreate()
    {
        return $this->create;
    }

    /**
     * Set read.
     *
     * @param string $read
     *
     * @return ArosAcos
     */
    public function setRead($read)
    {
        $this->read = $read;

        return $this;
    }

    /**
     * Get read.
     *
     * @return string
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * Set update.
     *
     * @param string $update
     *
     * @return ArosAcos
     */
    public function setUpdate($update)
    {
        $this->update = $update;

        return $this;
    }

    /**
     * Get update.
     *
     * @return string
     */
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     * Set delete.
     *
     * @param string $delete
     *
     * @return ArosAcos
     */
    public function setDelete($delete)
    {
        $this->delete = $delete;

        return $this;
    }

    /**
     * Get delete.
     *
     * @return string
     */
    public function getDelete()
    {
        return $this->delete;
    }
}
