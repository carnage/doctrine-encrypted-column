<?php

namespace Carnage\EncryptedColumn\Container;

use Psr\Container\ContainerInterface;

class VersionedContainer implements ContainerInterface
{
    private $services;

    public function set(VersionedInterface $service)
    {
        $this->services[$service->getIdentifier()] = $service;
    }

    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException(
                sprintf(
                    'Unable to find service %s, services available %s',
                    $id,
                    json_encode(array_keys($this->services), true)
                )
            );
        }

        return $this->services[$id];
    }

    public function has($id)
    {
        return isset($this->services[$id]);
    }
}
