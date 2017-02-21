<?php

namespace Carnage\EncryptedColumn\Serializer;

use Carnage\EncryptedColumn\ValueObject\IdentityInterface;
use Carnage\EncryptedColumn\ValueObject\SerializerIdentity;

class PhpSerializer implements SerializerInterface
{
    const IDENTITY = 'php';

    public function serialize($data)
    {
        return serialize($data);
    }

    public function unserialize($data)
    {
        return unserialize($data);
    }

    public function getIdentifier(): IdentityInterface
    {
        return new SerializerIdentity(self::IDENTITY);
    }
}
