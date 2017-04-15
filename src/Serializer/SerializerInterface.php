<?php

namespace Carnage\EncryptedColumn\Serializer;

use Carnage\EncryptedColumn\Container\VersionedInterface;

interface SerializerInterface extends VersionedInterface
{
    public function serialize($data);

    public function unserialize($data);
}