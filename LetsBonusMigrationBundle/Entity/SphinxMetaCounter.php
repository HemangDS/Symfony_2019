<?php

namespace iFlair\LetsBonusMigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SphinxMetaCounter.
 *
 * @ORM\Table(name="sphinx_meta_counter")
 * @ORM\Entity
 */
class SphinxMetaCounter
{
    /**
     * @var int
     *
     * @ORM\Column(name="counter_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $counterId;

    /**
     * @var int
     *
     * @ORM\Column(name="max_userssac_id", type="integer", nullable=false)
     */
    private $maxUserssacId;

    /**
     * Get counterId.
     *
     * @return int
     */
    public function getCounterId()
    {
        return $this->counterId;
    }

    /**
     * Set maxUserssacId.
     *
     * @param int $maxUserssacId
     *
     * @return SphinxMetaCounter
     */
    public function setMaxUserssacId($maxUserssacId)
    {
        $this->maxUserssacId = $maxUserssacId;

        return $this;
    }

    /**
     * Get maxUserssacId.
     *
     * @return int
     */
    public function getMaxUserssacId()
    {
        return $this->maxUserssacId;
    }
}
