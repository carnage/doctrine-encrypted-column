<?php

namespace Carnage\DceTest;

use Doctrine\ORM\Mapping as ORM;
use DoctrineEncrypt\Configuration as ORME;

/**
 * @ORM\Entity
 */
class Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="text")
     * @ORME\Encrypted
     * @var string
     */
    protected $secret_data;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getSecretData()
    {
        return $this->secret_data;
    }

    /**
     * @param mixed $secret_data
     */
    public function setSecretData($secret_data)
    {
        $this->secret_data = $secret_data;
    }
}