<?php

namespace Carnage\EncryptedColumn\Encryptor;

use Carnage\EncryptedColumn\Exception\PopArtPenguinException;
use Carnage\EncryptedColumn\ValueObject\EncryptorIdentity;
use Carnage\EncryptedColumn\ValueObject\IdentityInterface;
use phpseclib\Crypt\Base;
use phpseclib\Crypt\Rijndael;

class LegacyEncryptor implements EncryptorInterface
{
    const IDENTITY = 'legacy';
    /**
     * @var string
     */
    private $secret;

    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    public function encrypt($data)
    {
        throw new PopArtPenguinException();
    }

    public function decrypt($data)
    {
        $cipher = new Rijndael(Base::MODE_ECB);
        $cipher->setBlockLength(256);
        $cipher->setKey($this->secret);
        $cipher->padding = false;

        return trim($cipher->decrypt(base64_decode($data)));
    }

    public function getIdentifier(): IdentityInterface
    {
        return new EncryptorIdentity(self::IDENTITY);
    }
}