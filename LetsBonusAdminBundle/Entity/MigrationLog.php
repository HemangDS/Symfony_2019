<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MigrationLog.
 *
 * @ORM\Table(name="lb_migration_log")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\MigrationLogRepository")
 */
class MigrationLog
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
     * @ORM\Column(name="table_name", type="string", length=255)
     */
    private $tablename;


    /**
     * @var int
     *
     * @ORM\Column(name="last_processed_id", type="integer")
     */
    private $lastProcessedId;

    /**
     * @var int
     *
     * @ORM\Column(name="last_id", type="integer")
     */
    private $lastId;


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
     * Set id.
     *
     * @param int $id
     *
     * @return MigrationLog
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get lastProcessedId.
     *
     * @return int
     */
    public function getLastProcessedId()
    {
        return $this->lastProcessedId;
    }

    /**
     * Set lastProcessedId.
     *
     * @param int $lastProcessedId
     *
     * @return MigrationLog
     */
    public function setLastProcessedId($lastProcessedId)
    {
        $this->lastProcessedId = $lastProcessedId;

        return $this;
    }

    /**
     * Get lastId.
     *
     * @return int
     */
    public function getLastId()
    {
        return $this->lastId;
    }

    /**
     * Set lastId.
     *
     * @param int $lastId
     *
     * @return MigrationLog
     */
    public function setlastId($lastId)
    {
        $this->lastId = $lastId;

        return $this;
    }



    /**
     * Set status.
     *
     * @param bool $status
     *
     * @return MigrationLog
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
     * Set tablename.
     *
     * @param string $tablename
     *
     * @return MigrationLog
     */
    public function setTablename($tablename)
    {
        $this->tablename = $tablename;

        return $this;
    }

    /**
     * Get tablename.
     *
     * @return string
     */
    public function getTablename()
    {
        return $this->tablename;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Collection
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
     * @return Collection
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
}
