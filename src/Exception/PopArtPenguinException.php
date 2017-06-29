<?php

namespace Carnage\EncryptedColumn\Exception;

class PopArtPenguinException extends \BadMethodCallException
{
    public function __construct()
    {
        parent::__construct(
            'The encryption class you attempted to use is not considered secure and is only suitable for creating pop art penguins https://blog.filippo.io/the-ecb-penguin/'
        );
    }
}
