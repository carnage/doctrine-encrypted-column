<?php

namespace Carnage\EncryptedColumn\Encryptor;

use Carnage\EncryptedColumn\Container\VersionedInterface;
use Carnage\EncryptedColumn\ValueObject\Key;

interface EncryptorInterface extends VersionedInterface
{
    public function encrypt($data, Key $key);

    public function decrypt($data, Key $key);
}