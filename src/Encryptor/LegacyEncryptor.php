<?php

namespace Carnage\EncryptedColumn\Encryptor;

use Carnage\EncryptedColumn\Exception\PopArtPenguinException;
use Carnage\EncryptedColumn\ValueObject\EncryptorIdentity;
use Carnage\EncryptedColumn\ValueObject\IdentityInterface;
use Carnage\EncryptedColumn\ValueObject\Key;
use phpseclib\Crypt\Base;
use phpseclib\Crypt\Rijndael;

class LegacyEncryptor implements EncryptorInterface
{
    const IDENTITY = 'legacy';

    public function encrypt($data, Key $key)
    {
        throw new PopArtPenguinException();
    }

    public function decrypt($data, Key $key)
    {
        $cipher = new Rijndael(Base::MODE_ECB);
        $cipher->setBlockLength(256);
        $cipher->setKey($key->getKeyInfo());
        $cipher->padding = false;

        return trim($cipher->decrypt(base64_decode($data)));
    }

    public function getIdentifier(): IdentityInterface
    {
        return new EncryptorIdentity(self::IDENTITY);
    }
}