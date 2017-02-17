<?php

namespace Carnage\EncryptedColumn\Service;

use Carnage\EncryptedColumn\Dbal\EncryptedColumn;
use Carnage\EncryptedColumn\Encryptor\DummyEncryptor;
use Carnage\EncryptedColumn\Encryptor\EncryptorInterface;
use Carnage\EncryptedColumn\Serializer\PhpSerializer;
use Carnage\EncryptedColumn\Serializer\SerializerInterface;
use Carnage\EncryptedColumn\ValueObject\EncryptedColumn as EncryptedColumnVO;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;
use ProxyManager\Proxy\ValueHolderInterface;
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
     * EncryptionService constructor.
     * @param EncryptorInterface $encryptor
     * @param SerializerInterface $serializer
     * @param ContainerInterface $encryptors
     * @param ContainerInterface $serializers
     */
    public function __construct(
        EncryptorInterface $encryptor,
        SerializerInterface $serializer,
        ContainerInterface $encryptors,
        ContainerInterface $serializers
    ) {
        $this->encryptor = $encryptor;
        $this->serializer = $serializer;
        $this->encryptors = $encryptors;
        $this->serializers = $serializers;
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
            /** @var LazyLoadingInterface|ValueHolderInterface $value */
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

        $data = $this->encryptor->encrypt($this->serializer->serialize($value));

        return new EncryptedColumnVO(
            get_class($value),
            $data,
            $this->encryptor->getIdentifier(),
            $this->serializer->getIdentifier()
        );
    }

    /**
     * @param EncryptedColumnVO $value
     * @return \Closure
     */
    private function createInitializer(EncryptedColumnVO $value): \Closure
    {
        $serializer = $this->serializers->get($value->getSerializer());
        $encryptor = $this->encryptors->get($value->getEncryptor());

        return function (& $wrappedObject, LazyLoadingInterface $proxy, $method, array $parameters, & $initializer) use ($serializer, $encryptor, $value) {
            $initializer = null;
            $wrappedObject = $serializer->unserialize($encryptor->decrypt($value->getData()));

            return true;
        };
    }
}
