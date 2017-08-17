<?php

namespace Brouzie\Mailer\Container;

use Brouzie\Mailer\Exception\ServiceNotFoundException;
use Psr\Container\ContainerInterface;

class SimpleServiceLocator implements ContainerInterface
{
    private $services;

    public function __construct(array $services)
    {
        $this->services = $services;
    }

    public function has($id)
    {
        return isset($this->services[$id]);
    }

    public function get($id)
    {
        if (isset($this->services[$id])) {
            return $this->services[$id];
        }

        throw new ServiceNotFoundException($id, null, array_keys($this->services));
    }
}
