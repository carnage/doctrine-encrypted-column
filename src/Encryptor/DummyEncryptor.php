<?php

namespace Carnage\EncryptedColumn\Encryptor;

class DummyEncryptor
{
    public function encrypt($data)
    {
        return 'Encrypted (honest): ' . $data;
    }

    public function decrypt($data)
    {
        return substr($data, 20);
    }
}