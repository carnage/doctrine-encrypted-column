<?php

namespace Carnage\EncryptedColumn;

use Carnage\EncryptedColumn\Dbal\EncryptedColumn;
use Carnage\EncryptedColumn\Encryptor\HaliteEncryptor;
use Carnage\EncryptedColumn\Serializer\PhpSerializer;
use Carnage\EncryptedColumn\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;

class Configuration
{
    public static function register(EntityManagerInterface $em, $keypath)
    {
        EncryptedColumn::create(new EncryptionService(new HaliteEncryptor($keypath), new PhpSerializer()));
        $conn = $em->getConnection();
        $conn->getDatabasePlatform()->registerDoctrineTypeMapping(EncryptedColumn::ENCRYPTED, EncryptedColumn::ENCRYPTED);
    }
}