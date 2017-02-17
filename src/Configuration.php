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
        $encryptor = new HaliteEncryptor($keypath);
        $serializer = new PhpSerializer();

        $encryptors = new VersionedContainer();
        $encryptors->set($encryptor);

        $serializers = new VersionedContainer();
        $serializers->set($serializer);

        return new EncryptionService($encryptor, $serializer, $encryptors, $serializers);
    }
}