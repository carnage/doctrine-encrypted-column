<?php

namespace Carnage\EncryptedColumn\ValueObject;

class SerializerIdentity implements IdentityInterface
{
    /**
     * @var string
     */
    private $identity;

    public function __construct(string $identity)
    {
        $this->identity = $identity;
    }

    /**
     * @return string
     */
    public function getIdentity(): string
    {
        return $this->identity;
    }

    public function toString(): string
    {
        return $this->identity;
    }

    public function equals(IdentityInterface $other): bool
    {
        return $other instanceof SerializerIdentity && $this->identity === $other->identity;
    }
}
