<?php

namespace Carnage\EncryptedColumn\Encryptor;

use Carnage\EncryptedColumn\ValueObject\EncryptorIdentity;
use Carnage\EncryptedColumn\ValueObject\IdentityInterface;
use Carnage\EncryptedColumn\ValueObject\Key;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric;

class HaliteEncryptor implements EncryptorInterface
{
    const IDENTITY = 'halite';

    public function encrypt($data, Key $key)
    {
        return Symmetric\Crypto::encrypt($data, $this->loadKey($key));
    }

    public function decrypt($data, Key $key)
    {
        return Symmetric\Crypto::decrypt($data, $this->loadKey($key));
    }

    public function getIdentifier(): IdentityInterface
    {
        return new EncryptorIdentity(self::IDENTITY);
    }

    /**
     * @return Symmetric\EncryptionKey
     * @throws \ParagonIE\Halite\Alerts\CannotPerformOperation
     */
    private function loadKey(Key $key)
    {
        return KeyFactory::loadEncryptionKey($key->getKeyInfo());
    }
}