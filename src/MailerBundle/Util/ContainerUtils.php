<?php

namespace Brouzie\MailerBundle\Util;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ContainerUtils
{
    /**
     * Finds all services with the given tag name and order them by their priority.
     *
     * @param string $tagName
     * @param ContainerBuilder $container
     *
     * @return Reference[]
     */
    public static function findAndSortTaggedServices(string $tagName, ContainerBuilder $container): array
    {
        $services = array();

        foreach ($container->findTaggedServiceIds($tagName, true) as $serviceId => $attributes) {
            $priority = $attributes[0]['priority'] ?? 0;
            $services[$priority][] = new Reference($serviceId);
        }

        if ($services) {
            krsort($services);
            $services = array_merge(...$services);
        }

        return $services;
    }
}
