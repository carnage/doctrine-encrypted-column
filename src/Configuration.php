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
    public static function register(EntityManagerInterface $em, $keypath)
    {
        EncryptedColumn::create(self::buildEncryptionService($keypath));
        $conn = $em->getConnection();
        $conn->getDatabasePlatform()->registerDoctrineTypeMapping(EncryptedColumn::ENCRYPTED, EncryptedColumn::ENCRYPTED);
    }

    /**
     * @param $keypath
     * @return EncryptionService
     */
    private static function buildEncryptionService($keypath): EncryptionService
    {
        $encryptors = self::buildEncryptorsContainer($keypath);
        $encryptor = $encryptors->get(HaliteEncryptor::IDENTITY);

        $serializers = self::buildSerilaizerContainer();
        $serializer = $serializers->get(PhpSerializer::IDENTITY);

        return new EncryptionService($encryptor, $serializer, $encryptors, $serializers);
    }

    /**
     * @param $encryptor
     * @return VersionedContainer
     */
    private static function buildEncryptorsContainer($keypath): VersionedContainer
    {
        $encryptor = new HaliteEncryptor($keypath);
        $encryptors = new VersionedContainer();
        $encryptors->set($encryptor);
        return $encryptors;
    }

    /**
     * @param $serializer
     * @return VersionedContainer
     */
    private static function buildSerilaizerContainer(): VersionedContainer
    {
        $serializer = new PhpSerializer();
        $serializers = new VersionedContainer();
        $serializers->set($serializer);
        return $serializers;
    }
}