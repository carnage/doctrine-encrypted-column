<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 10/09/16
 * Time: 16:08
 */
namespace Carnage\EncryptedColumn\Encryptor;

interface EncryptorInterface
{
    public function encrypt($data);

    public function decrypt($data);

    public function getIdentity();
}