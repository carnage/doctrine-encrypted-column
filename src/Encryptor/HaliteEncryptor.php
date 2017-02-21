<?php

namespace Carnage\EncryptedColumn\Encryptor;

use Carnage\EncryptedColumn\ValueObject\EncryptorIdentity;
use Carnage\EncryptedColumn\ValueObject\IdentityInterface;
use ParagonIE\Halite\Halite;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric;

class HaliteEncryptor implements EncryptorInterface
{
    const IDENTITY = 'halite';
    /**
     * @var string
     */
    private $keypath;
    private $key;

    public function __construct($keypath)
    {
        $this->keypath = $keypath;
    }

    public function encrypt($data)
    {
        return Symmetric\Crypto::encrypt($data, $this->loadKey());
    }

    public function decrypt($data)
    {
        return Symmetric\Crypto::decrypt($data, $this->loadKey());
    }

    public function getIdentifier(): IdentityInterface
    {
        return new EncryptorIdentity(self::IDENTITY);
    }

    /**
     * @return Symmetric\EncryptionKey
     * @throws \ParagonIE\Halite\Alerts\CannotPerformOperation
     */
    private function loadKey()
    {
        if ($this->key === null) {
            $this->key = KeyFactory::loadEncryptionKey($this->keypath);
        }
        
        return $this->key;
    }

}