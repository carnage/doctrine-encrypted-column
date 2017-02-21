<?php

namespace Carnage\EncryptedColumn\ValueObject;

interface IdentityInterface
{
    public function getIdentity(): string;

    public function toString(): string;

    public function equals(IdentityInterface $other): bool;
}