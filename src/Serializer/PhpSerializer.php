<?php

namespace Carnage\EncryptedColumn\Serializer;

class PhpSerializer
{
    public function serialize($data)
    {
        return serialize($data);
    }

    public function unserialize($data)
    {
        return unserialize($data);
    }
}