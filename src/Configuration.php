<?php

namespace Carnage\EncryptedColumn;

use Carnage\EncryptedColumn\Container\VersionedContainer;
use Carnage\EncryptedColumn\Dbal\EncryptedColumn;
use Carnage\EncryptedColumn\Encryptor\HaliteEncryptor;
use Carnage\EncryptedColumn\Serializer\PhpSerializer;
use Carnage\EncryptedColumn\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;

class Configuration
{
    public static function register(EntityManagerInterface $em, string $keypath)
    {
        EncryptedColumn::create(self::buildEncryptionService($keypath));
        $conn = $em->getConnection();
        $conn->getDatabasePlatform()->registerDoctrineTypeMapping(
            EncryptedColumn::ENCRYPTED,
            EncryptedColumn::ENCRYPTED
        );
    }

    private static function buildEncryptionService(string $keypath): EncryptionService
    {
        $encryptors = self::buildEncryptorsContainer($keypath);
        $serializers = self::buildSerilaizerContainer();
        return new EncryptionService(
            $encryptors->get(HaliteEncryptor::IDENTITY),
            $serializers->get(PhpSerializer::IDENTITY),
            $encryptors,
            $serializers
        );
    }

    private static function buildEncryptorsContainer(string $keypath): VersionedContainer
    {
        return new VersionedContainer(new HaliteEncryptor($keypath));
    }

    private static function buildSerilaizerContainer(): VersionedContainer
    {
        return new VersionedContainer(new PhpSerializer());
    }
}