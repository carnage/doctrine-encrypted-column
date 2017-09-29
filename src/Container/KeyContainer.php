<?php

namespace Carnage\EncryptedColumn\Container;

use Carnage\EncryptedColumn\ValueObject\Key;
use Psr\Container\ContainerInterface;

final class KeyContainer implements ContainerInterface
{
    /**
     * @var array
     */
    private $keys = [];

    public function addKey(Key $key)
    {
        $this->keys[$key->getIdentifier()->toString()] = $key;
    }

    public function tagKey($tag, $id)
    {
        $this->keys[$tag] = $this->keys[$id];
    }

    public function get($id)
    {
        if (!$this->has($id)) {
            throw NotFoundException::serviceNotFoundInContainer($id, $this->keys);
        }

        return $this->keys[$id];
    }

    public function has($id): bool
    {
        return isset($this->keys[$id]);
    }
}
