<?php

namespace Carnage\EncryptedColumn\ValueObject;

use Carnage\EncryptedColumn\Encryptor\HaliteEncryptor;
use Carnage\EncryptedColumn\Serializer\PhpSerializer;

class EncryptedColumn implements \JsonSerializable
{
    /**
     * @var string
     */
    private $classname;

    /**
     * @var string
     */
    private $data;

    /**
     * @var EncryptorIdentity
     */
    private $encryptor;

    /**
     * @var SerializerIdentity
     */
    private $serializer;

    /**
     * @var KeyIdentity
     */
    private $key;

    public function __construct(
        string $classname,
        string $data,
        EncryptorIdentity $encryptor,
        SerializerIdentity $serializer,
        KeyIdentity $key
    ) {
        $this->classname = $classname;
        $this->data = $data;
        $this->encryptor = $encryptor;
        $this->serializer = $serializer;
        $this->key = $key;
    }

    public static function fromArray(array $data)
    {
        return new self(
            $data['classname'],
            $data['data'],
            new EncryptorIdentity($data['encryptor']),
            new SerializerIdentity($data['serializer']),
            new KeyIdentity($data['keyid'])
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'classname' => $this->classname,
            'data' => $this->data,
            'encryptor' => $this->encryptor->toString(),
            'serializer' => $this->serializer->toString(),
            'keyid' => $this->key->toString(),
        ];
    }

    /**
     * @return mixed
     */
    public function getClassname(): string
    {
        return $this->classname;
    }

    /**
     * @return mixed
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @return EncryptorIdentity
     */
    public function getEncryptorIdentifier(): EncryptorIdentity
    {
        return $this->encryptor;
    }

    /**
     * @return SerializerIdentity
     */
    public function getSerializerIdentifier(): SerializerIdentity
    {
        return $this->serializer;
    }

    /**
     * @return KeyIdentity
     */
    public function getKeyIdentifier(): KeyIdentity
    {
        return $this->key;
    }

    public function needsReencryption(EncryptorIdentity $encryptor, SerializerIdentity $serializer): bool
    {
        return $encryptor->equals($this->encryptor) && $serializer->equals($this->serializer);
    }
}