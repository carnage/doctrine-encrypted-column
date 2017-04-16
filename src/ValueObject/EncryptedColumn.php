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
     * EncryptedColumn constructor.
     * @param $classname
     * @param $data
     */
    public function __construct(
        string $classname,
        string $data,
        EncryptorIdentity $encryptor,
        SerializerIdentity $serializer
    ) {
        $this->classname = $classname;
        $this->data = $data;
        $this->encryptor = $encryptor;
        $this->serializer = $serializer;
    }

    public static function fromArray(array $data)
    {
        // If an old version has saved data, these fields won't be available
        // Default to the only services available in V0.1
        if (!isset($data['serializer'])) {
            return new self(
                $data['classname'],
                $data['data'],
                new EncryptorIdentity(HaliteEncryptor::IDENTITY),
                new SerializerIdentity(PhpSerializer::IDENTITY)
            );
        }

        return new self(
            $data['classname'],
            $data['data'],
            new EncryptorIdentity($data['encryptor']),
            new SerializerIdentity($data['serializer'])
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'classname' => $this->classname,
            'data' => $this->data,
            'encryptor' => $this->encryptor->toString(),
            'serializer' => $this->serializer->toString()
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

    public function needsReencryption(EncryptorIdentity $encryptor, SerializerIdentity $serializer): bool
    {
        return $encryptor->equals($this->encryptor) && $serializer->equals($this->serializer);
    }
}