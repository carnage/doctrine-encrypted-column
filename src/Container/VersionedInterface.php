<?php

namespace Carnage\EncryptedColumn\Container;

use Carnage\EncryptedColumn\ValueObject\IdentityInterface;

interface VersionedInterface
{
    public function getIdentifier(): IdentityInterface;
}