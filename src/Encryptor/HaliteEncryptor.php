<?php

namespace Carnage\EncryptedColumn\Encryptor;

use ParagonIE\Halite\Halite;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric;

class HaliteEncryptor implements EncryptorInterface
{
    const IDENTITY = 'halite:0.1:' . Halite::VERSION;
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

    public function getIdentifier(): string
    {
        return self::IDENTITY;
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