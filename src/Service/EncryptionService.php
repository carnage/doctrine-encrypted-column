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
     * @var array
     */
    private $originalValues = [];

    /**
     * EncryptionService constructor.
     * @param EncryptorInterface $encryptor
     * @param SerializerInterface $serializer
     */
    public function __construct(EncryptorInterface $encryptor, SerializerInterface $serializer)
    {
        $this->encryptor = $encryptor;
        $this->serializer = $serializer;
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
            if (!$value->isProxyInitialized()) {
                //put a method on object to check if needs reencrypting check that here
                // $value->needsReencryption()
                //if data hasn't been encrypted, we don't need to change it; set it back to what it was at load
                return $this->originalValues[spl_object_hash($value)];
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
            $this->encryptor->getIdentity(),
            $this->serializer->getIdentity()
        );
    }

    /**
     * @param EncryptedColumnVO $value
     * @return \Closure
     */
    private function createInitializer(EncryptedColumnVO $value): \Closure
    {
        $serializer = $this->serializer;
        $encryptor = $this->encryptor;

        return function (& $wrappedObject, LazyLoadingInterface $proxy, $method, array $parameters, & $initializer) use ($serializer, $encryptor, $value) {
            $initializer = null;
            $wrappedObject = $serializer->unserialize($encryptor->decrypt($value->getData()));

            return true;
        };
    }
}
