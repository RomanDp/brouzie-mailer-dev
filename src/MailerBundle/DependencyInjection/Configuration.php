<?php

namespace Brouzie\MailerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('brouzie_mailer');

        $rootNode
            ->children()
                ->scalarNode('sender')
                ->defaultNull()
                ->info('Service name of the cache pool.')
                ->example('cache.app')
            ->end()
        ;

        return $treeBuilder;
    }
}
