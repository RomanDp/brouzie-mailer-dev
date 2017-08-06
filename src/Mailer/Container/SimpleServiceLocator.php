<?php

namespace Brouzie\Mailer\Container;

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
        //FIXME: Implement get() method.
    }
}
