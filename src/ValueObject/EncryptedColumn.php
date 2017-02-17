<?php

namespace Carnage\EncryptedColumn\ValueObject;

use Carnage\EncryptedColumn\Encryptor\HaliteEncryptor;
use Carnage\EncryptedColumn\Serializer\PhpSerializer;

class EncryptedColumn implements \JsonSerializable
{
    private $classname;
    private $data;
    private $encryptor;
    private $serializer;

    /**
     * EncryptedColumn constructor.
     * @param $classname
     * @param $data
     */
    public function __construct(
        $classname,
        $data,
        $encryptor = HaliteEncryptor::IDENTITY,
        $serializer = PhpSerializer::IDENTITY
    ) {
        $this->classname = $classname;
        $this->data = $data;
        $this->encryptor = $encryptor;
        $this->serializer = $serializer;
    }

    public static function fromArray(array $data)
    {
        if(!isset($data['serializer'])) {
            return new self($data['classname'], $data['data']);
        }

        return new self($data['classname'], $data['data'], $data['encryptor'], $data['serializer']);
    }

    function jsonSerialize()
    {
        return [
            'classname' => $this->classname,
            'data' => $this->data,
            'encryptor' => $this->encryptor,
            'serializer' => $this->serializer
        ];
    }

    /**
     * @return mixed
     */
    public function getClassname()
    {
        return $this->classname;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getEncryptor(): string
    {
        return $this->encryptor;
    }

    /**
     * @return string
     */
    public function getSerializer(): string
    {
        return $this->serializer;
    }
}