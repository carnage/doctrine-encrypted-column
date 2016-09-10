<?php

namespace Carnage\EncryptedColumn\Listener;

use Carnage\EncryptedColumn\Dbal\EncryptedColumn;
use Carnage\EncryptedColumn\Encryptor\DummyEncryptor;
use Carnage\EncryptedColumn\Serializer\PhpSerializer;
use Carnage\EncryptedColumn\ValueObject\EncryptedColumn as EncryptedColumnVO;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;
use ProxyManager\Proxy\ValueHolderInterface;

class EncryptListener 
{
    /**
     * @var DummyEncryptor
     */
    private $encryptor;
    
    /**
     * @var PhpSerializer
     */
    private $serializer;

    /**
     * @var array
     */
    private $originalValues = [];

    /**
     * EncryptListener constructor.
     * @param DummyEncryptor $encryptor
     * @param PhpSerializer $serializer
     */
    public function __construct(DummyEncryptor $encryptor, PhpSerializer $serializer)
    {
        $this->encryptor = $encryptor;
        $this->serializer = $serializer;
    }


    public function preUpdate($entity, LifecycleEventArgs $args)
    {
        $entityManager = $args->getObjectManager();
        $classMetadata = $entityManager->getClassMetadata(get_class($entity));

        foreach ($classMetadata->getFieldNames() as $fieldName) {
            if ($classMetadata->getTypeOfField($fieldName) === EncryptedColumn::ENCRYPTED) {
                $this->encryptField($classMetadata->getReflectionClass()->getProperty($fieldName), $entity);
            }
        }
    }
    
    public function prePersist($entity, LifecycleEventArgs $args)
    {
        $entityManager = $args->getObjectManager();
        $classMetadata = $entityManager->getClassMetadata(get_class($entity));

        foreach ($classMetadata->getFieldNames() as $fieldName) {
            if ($classMetadata->getTypeOfField($fieldName) === EncryptedColumn::ENCRYPTED) {
                $this->encryptField($classMetadata->getReflectionClass()->getProperty($fieldName), $entity);
            }
        }
    }
    
    public function postLoad($entity, LifecycleEventArgs $args)
    {
        $entityManager = $args->getObjectManager();
        $classMetadata = $entityManager->getClassMetadata(get_class($entity));
        
        foreach ($classMetadata->getFieldNames() as $fieldName) {
            if ($classMetadata->getTypeOfField($fieldName) === EncryptedColumn::ENCRYPTED) {
                $this->replaceWithProxy($classMetadata->getReflectionClass()->getProperty($fieldName), $entity);
            }
        }
    }

    private function replaceWithProxy(\ReflectionProperty $field, $entity)
    {
        /** @var EncryptedColumnVO $value */
        $value = $field->getValue($entity);

        $this->originalValues[spl_object_hash($entity)][$field->getName()] = $value;
        
        $serializer = $this->serializer;
        $encryptor = $this->encryptor;
        
        $initializer = function (& $wrappedObject, LazyLoadingInterface $proxy, $method, array $parameters, & $initializer) use ($serializer, $encryptor, $value) {
            $initializer   = null;
            $wrappedObject = $serializer->unserialize($encryptor->decrypt($value->getData()));

            return true;
        };
        $factory     = new LazyLoadingValueHolderFactory();
        $proxy = $factory->createProxy($value->getClassname(), $initializer);

        $field->setValue($entity, $proxy);
    }

    private function encryptField(\ReflectionProperty $field, $entity)
    {
        $value = $field->getValue($entity);

        if ($value instanceof LazyLoadingInterface) {
            /** @var LazyLoadingInterface|ValueHolderInterface $value */
            if (!$value->isProxyInitialized()) {
                //if data hasn't been encrypted, we don't need to change it; set it back to what it was at load
                $field->setValue($this->originalValues[spl_object_hash($entity)][$field->getName()]);
                return;
            }

            //we don't want to encrypt a proxy.
            $value = $value->getWrappedValueHolderValue();
            //@TODO figure out how we can return early if the object is identical to the original, we probably need a clone to compare to.
        }

        if (!is_object($value)) {
            throw new \Exception('This column type only supports encrypting objects');
        }

        $data = $this->encryptor->encrypt($this->serializer->serialize($value));

        $field->setValue(new EncryptedColumnVO(get_class($value), $data));
    }
}