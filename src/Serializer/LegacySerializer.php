<?php

namespace Carnage\EncryptedColumn\Serializer;

use Carnage\EncryptedColumn\ValueObject\IdentityInterface;
use Carnage\EncryptedColumn\ValueObject\SerializerIdentity;
use Carnage\EncryptedColumn\ValueObject\ValueHolder;

class LegacySerializer implements SerializerInterface
{
    const IDENTITY = 'legacy';

    public function serialize($data)
    {
        throw new \Exception('This class is for read only access to legacy data');
    }

    public function unserialize($data)
    {
        return new ValueHolder($data);
    }

    public function getIdentifier(): IdentityInterface
    {
        return new SerializerIdentity(self::IDENTITY);
    }
}
