<?php

namespace Carnage\EncryptedColumn\Serializer;

class PhpSerializer implements SerializerInterface
{
    const IDENTITY = 'php:0.1';

    public function serialize($data)
    {
        return serialize($data);
    }

    public function unserialize($data)
    {
        return unserialize($data);
    }

    public function getIdentifier(): string
    {
        return self::IDENTITY;
    }
}
