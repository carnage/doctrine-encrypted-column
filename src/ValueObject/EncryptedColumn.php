<?php

namespace Carnage\EncryptedColumn\ValueObject;

class EncryptedColumn implements \JsonSerializable
{
    private $classname;
    private $data;

    /**
     * EncryptedColumn constructor.
     * @param $classname
     * @param $data
     */
    public function __construct($classname, $data)
    {
        $this->classname = $classname;
        $this->data = $data;
    }

    public static function fromArray(array $data)
    {
        return new self($data['classname'], $data['data']);
    }

    function jsonSerialize()
    {
        return ['classname' => $this->classname, 'data' => $this->data];
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
}