<?php
namespace Carnage\EncryptedColumn\Dbal;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Carnage\EncryptedColumn\ValueObject\EncryptedColumn as EncryptedColumnVO;

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

        $decoded = $this->decodeJson($value);

        return EncryptedColumnVO::fromArray($decoded);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return json_encode($value);
    }

    /**
     * Based on: https://github.com/schmittjoh/serializer/blob/master/src/JMS/Serializer/JsonDeserializationVisitor.php
     *
     * @param $value
     * @return mixed
     * @throws ConversionException
     */
    private function decodeJson($value)
    {
        $decoded = json_decode($value, true);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                if (!is_array($decoded)) {
                    throw ConversionException::conversionFailed($value, 'Json was not an array');
                }
                return $decoded;
            case JSON_ERROR_DEPTH:
                throw ConversionException::conversionFailed($value, 'Could not decode JSON, maximum stack depth exceeded.');
            case JSON_ERROR_STATE_MISMATCH:
                throw ConversionException::conversionFailed($value, 'Could not decode JSON, underflow or the nodes mismatch.');
            case JSON_ERROR_CTRL_CHAR:
                throw ConversionException::conversionFailed($value, 'Could not decode JSON, unexpected control character found.');
            case JSON_ERROR_SYNTAX:
                throw ConversionException::conversionFailed($value, 'Could not decode JSON, syntax error - malformed JSON.');
            case JSON_ERROR_UTF8:
                throw ConversionException::conversionFailed($value, 'Could not decode JSON, malformed UTF-8 characters (incorrectly encoded?)');
            default:
                throw ConversionException::conversionFailed($value, 'Could not decode Json');
        }
    }
}
