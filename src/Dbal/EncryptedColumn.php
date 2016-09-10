<?php
namespace Carnage\EncryptedColumn\Dbal;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

/**
 * Object type for storing an encrypted column object as json in the db
 *
 * Class EncryptedColumn
 */
class EncryptedColumn extends Type
{
    const ENCRYPTED = 'encrypted';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getClobTypeDeclarationSQL($fieldDeclaration);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    public function getName()
    {
        return self::ENCRYPTED;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        $decoded = json_decode($value, true);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                break;
            default:
                throw ConversionException::conversionFailed($value, 'Could not decode Json');
        }

        return EncryptedColumnVO::fromArray($decoded);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return json_encode($value);
    }
}