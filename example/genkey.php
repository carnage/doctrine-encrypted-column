<?php
require "vendor/autoload.php";

use ParagonIE\Halite\KeyFactory;

$encKey = KeyFactory::generateEncryptionKey();
KeyFactory::save($encKey, './enc.key');