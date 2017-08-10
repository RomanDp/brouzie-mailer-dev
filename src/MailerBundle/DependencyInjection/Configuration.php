<?php

namespace Brouzie\MailerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
                ->append($this->getSenderNode())
            ->end()
        ;

//        $this->addEmailsSection($rootNode);

        return $treeBuilder;
    }

    private function getSenderNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('sender');

        $node
            ->isRequired()
            ->beforeNormalization()
            ->ifString()
                ->then(function($value) { return ['address' => $value]; })
            ->end()
            ->beforeNormalization()
            ->ifArray()
                ->then(function($value) {
                    if (!isset($value['address']) && reset($value)) {
                        return ['address' => key($value), 'name' => reset($value)];
                    }

                    return $value;
                })
            ->end()
            ->children()
                ->scalarNode('name')->end()
                ->scalarNode('address')->isRequired()->cannotBeEmpty()->end()
            ->end();

        return $node;
    }

    private function addEmailsSection(ArrayNodeDefinition $node)
    {
        $node
            ->fixXmlConfig('email')
            ->children()
                ->arrayNode('emails')
                    ->useAttributeAsKey('code')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('subject')->end()
                            ->scalarNode('body')->end()
                            ->scalarNode('body_html')->end()
                            ->scalarNode('template')->end()
                            ->fixXmlConfig('required_context_key')
                            ->arrayNode('required_context_keys')
                                ->defaultValue([])
                                ->prototype('scalar')->end()
                            ->end()
                            ->append($this->getSenderNode())
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
