<?php
namespace Carnage\EncryptedColumn\Dbal;

use Carnage\EncryptedColumn\Service\EncryptionService;
use Carnage\EncryptedColumn\ValueObject\ValueHolder;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Carnage\EncryptedColumn\ValueObject\EncryptedColumn as EncryptedColumnVO;

/**
 * Object type for reading from legacy encrypted column extensions and converting to a better format
 *
 * This type is a drop in replacement for the EncryptedColumn class but drops some strictness to allow for
 * reading data that is not in an expected format. You should only use this if you have existing data in
 * your database you wish to convert
 *
 * Class EncryptedColumn
 */
class EncryptedColumnLegacySupport extends Type
{
    const ENCRYPTED = 'encrypted';

    /**
     * @var EncryptionService
     */
    private $encryptionService;

    public static function create(EncryptionService $encryptionService)
    {
        Type::addType(EncryptedColumnLegacySupport::ENCRYPTED, EncryptedColumnLegacySupport::class);
        /** @var EncryptedColumnLegacySupport $instance */
        $instance = Type::getType(EncryptedColumnLegacySupport::ENCRYPTED);
        $instance->encryptionService = $encryptionService;
    }

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

        try {
            $decoded = $this->decodeJson($value);
        } catch (ConversionException $e) {
            //The data wasn't in the format we expected, assume it is legacy data which needs converting
            //Drop in some defaults to allow the library to handle it.
            $decoded = [
                'data' => $value,
                'classname' => ValueHolder::class,
                'serializer' => 'legacy',
                'encryptor' => 'legacy'
            ];
        }

        return $this->encryptionService->decryptField(EncryptedColumnVO::fromArray($decoded));
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        return json_encode($this->encryptionService->encryptField($value));
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
