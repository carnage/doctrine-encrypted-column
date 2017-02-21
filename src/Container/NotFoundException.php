<?php

namespace Carnage\EncryptedColumn\Container;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends \RuntimeException implements NotFoundExceptionInterface
{
    public static function serviceNotFoundInContainer($id, $services)
    {
        return new static(sprintf(
            'Unable to find service %s, services available %s',
            $id,
            json_encode(array_keys($services), true)
        ));
    }
}