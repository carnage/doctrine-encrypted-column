<?php
/**
 * Created by PhpStorm.
 * User: imhotek
 * Date: 10/09/16
 * Time: 16:08
 */
namespace Carnage\EncryptedColumn\Serializer;

interface SerializerInterface
{
    public function serialize($data);

    public function unserialize($data);

    public function getIdentity();
}