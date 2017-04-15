<?php

namespace Carnage\EncryptedColumn\Encryptor;

use Carnage\EncryptedColumn\Container\VersionedInterface;

interface EncryptorInterface extends VersionedInterface
{
    public function encrypt($data);

    public function decrypt($data);
}