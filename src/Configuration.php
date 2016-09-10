<?php

namespace Carnage\EncryptedColumn;

use Carnage\EncryptedColumn\Dbal\EncryptedColumn;
use Carnage\EncryptedColumn\Encryptor\DummyEncryptor;
use Carnage\EncryptedColumn\Encryptor\HaliteEncryptor;
use Carnage\EncryptedColumn\Listener\EncryptListener;
use Carnage\EncryptedColumn\Listener\MetadataListener;
use Carnage\EncryptedColumn\Serializer\PhpSerializer;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;

class Configuration
{
    public static function register(EntityManagerInterface $em, $keypath)
    {
        $metadataListener = new MetadataListener(new EncryptListener(new HaliteEncryptor($keypath), new PhpSerializer()));
        $evm = $em->getEventManager();
        $evm->addEventSubscriber($metadataListener);

        Type::addType(EncryptedColumn::ENCRYPTED, EncryptedColumn::class);
        $conn = $em->getConnection();
        $conn->getDatabasePlatform()->registerDoctrineTypeMapping(EncryptedColumn::ENCRYPTED, EncryptedColumn::ENCRYPTED);
    }
}