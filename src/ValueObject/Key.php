<?php

namespace Carnage\EncryptedColumn\ValueObject;

use ParagonIE\Halite\Util;

final class Key
{
    /**
     * @var KeyIdentity
     */
    private $identifier;

    /**
     * @var string
     */
    private $keyInfo;

    /**
     * Key constructor.
     * @param $keyInfo
     */
    public function __construct(string $keyInfo)
    {
        $this->identifier = new KeyIdentity(Util::safeSubstr(hash('sha256', $keyInfo), 0, 8));
        $this->keyInfo = $keyInfo;
    }

    /**
     * @return KeyIdentity
     */
    public function getIdentifier(): KeyIdentity
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getKeyInfo(): string
    {
        return $this->keyInfo;
    }
}
