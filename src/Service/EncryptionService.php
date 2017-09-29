<?php

namespace Carnage\EncryptedColumn\Service;

use Carnage\EncryptedColumn\Encryptor\EncryptorInterface;
use Carnage\EncryptedColumn\Serializer\SerializerInterface;
use Carnage\EncryptedColumn\ValueObject\EncryptedColumn as EncryptedColumnVO;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;
use ProxyManager\Proxy\VirtualProxyInterface;
use Psr\Container\ContainerInterface;

class EncryptionService
{
    /**
     * @var EncryptorInterface
     */
    private $encryptor;
    
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var EncryptedColumnVO[]
     */
    private $originalValues = [];

    /**
     * @var ContainerInterface
     */
    private $encryptors;

    /**
     * @var ContainerInterface
     */
    private $serializers;

    /**
     * @var ContainerInterface
     */
    private $keys;

    public function __construct(
        EncryptorInterface $encryptor,
        SerializerInterface $serializer,
        ContainerInterface $encryptors,
        ContainerInterface $serializers,
        ContainerInterface $keys
    ) {
        $this->encryptor = $encryptor;
        $this->serializer = $serializer;
        $this->encryptors = $encryptors;
        $this->serializers = $serializers;
        $this->keys = $keys;
    }

    public function decryptField(EncryptedColumnVO $value)
    {
        $initializer = $this->createInitializer($value);
        $factory = new LazyLoadingValueHolderFactory();
        $proxy = $factory->createProxy($value->getClassname(), $initializer);

        $this->originalValues[spl_object_hash($proxy)] = $value;

        return $proxy;
    }

    public function encryptField($value): EncryptedColumnVO
    {
        if ($value instanceof LazyLoadingInterface) {
            /** @var VirtualProxyInterface $value */
            // If the value hasn't been decrypted; it hasn't been changed. Don't bother reencrypting unless it
            // was encrypted using a different configuration
            if (!$value->isProxyInitialized()) {
                $original = $this->originalValues[spl_object_hash($value)];
                if (
                    !$original->needsReencryption($this->encryptor->getIdentifier(), $this->serializer->getIdentifier())
                ) {
                        return $original;
                }
            }

            //we don't want to encrypt a proxy.
            $value = $value->getWrappedValueHolderValue();
        }

        if (!is_object($value)) {
            throw new \Exception('This column type only supports encrypting objects');
        }

        $key = $this->keys->get('default');
        $data = $this->encryptor->encrypt($this->serializer->serialize($value), $key);

        return new EncryptedColumnVO(
            get_class($value),
            $data,
            $this->encryptor->getIdentifier(),
            $this->serializer->getIdentifier(),
            $key->getIdentifier()
        );
    }

    /**
     * @param EncryptedColumnVO $value
     * @return \Closure
     */
    private function createInitializer(EncryptedColumnVO $value): \Closure
    {
        $serializer = $this->serializers->get($value->getSerializerIdentifier()->toString());
        $encryptor = $this->encryptors->get($value->getEncryptorIdentifier()->toString());
        $key = $this->keys->get($value->getKeyIdentifier()->toString());

        return function(& $wrappedObject, LazyLoadingInterface $proxy, $method, array $parameters, & $initializer) use ($serializer, $encryptor, $key, $value) {
            $initializer = null;
            $wrappedObject = $serializer->unserialize($encryptor->decrypt($value->getData(), $key));

            return true;
        };
    }
}
