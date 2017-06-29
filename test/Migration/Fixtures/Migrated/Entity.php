<?php

namespace Carnage\EncryptedColumn\Tests\Migration\Fixtures\Migrated;

use Carnage\EncryptedColumn\ValueObject\ValueHolder;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="encrypted")
     * @var ValueHolder
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
        return $this->secret_data->getValue();
    }

    /**
     * @param mixed $secret_data
     */
    public function setSecretData($secret_data)
    {
        $this->secret_data = new ValueHolder($secret_data);
    }
}