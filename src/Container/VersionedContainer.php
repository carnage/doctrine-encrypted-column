<?php

namespace Carnage\EncryptedColumn\Container;

use Psr\Container\ContainerInterface;

final class VersionedContainer implements ContainerInterface
{
    /**
     * @var VersionedInterface[]
     */
    private $services;

    public function __construct(VersionedInterface ...$services)
    {
        foreach ($services as $service) {
            $this->services[$service->getIdentifier()->toString()] = $service;
        }
    }

    public function get($id): VersionedInterface
    {
        if (!$this->has($id)) {
            throw NotFoundException::serviceNotFoundInContainer($id, $this->services);
        }

        return $this->services[$id];
    }

    public function has($id): bool
    {
        return isset($this->services[$id]);
    }
}
