<?php

namespace Carnage\EncryptedColumn\Container;

interface VersionedInterface
{
    public function getIdentifier(): string;
}