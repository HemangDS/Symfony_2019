<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZanoxConfig.
 *
 * @ORM\Table(name="lb_zanoxconfig")
 * @ORM\Entity(repositoryClass="iFlair\LetsBonusAdminBundle\Entity\ZanoxConfigRepository")
 */
class ZanoxConfig
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
     * @ORM\Column(name="ConnectId", type="string", length=255)
     */
    private $connectId;

    /**
     * @var string
     *
     * @ORM\Column(name="SecretKey", type="string", length=255)
     */
    private $secretKey;

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
     * Set connectId.
     *
     * @param string $connectId
     *
     * @return ZanoxConfig
     */
    public function setConnectId($connectId)
    {
        $this->connectId = $connectId;

        return $this;
    }

    /**
     * Get connectId.
     *
     * @return string
     */
    public function getConnectId()
    {
        return $this->connectId;
    }

    /**
     * Set secretKey.
     *
     * @param string $secretKey
     *
     * @return ZanoxConfig
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;

        return $this;
    }

    /**
     * Get secretKey.
     *
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }
}
