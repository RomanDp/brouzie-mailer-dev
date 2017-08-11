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
                ->append($this->getContextNode())
                ->append($this->getHeadersNode())
            ->end()
        ;

        $this->addEmailsSection($rootNode);

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
                    if (!array_key_exists('address', $value)) {
                        return ['address' => key($value), 'name' => reset($value)];
                    }

                    return $value;
                })
            ->end()
            ->children()
                ->scalarNode('address')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('name')->end()
            ->end();

        return $node;
    }

    private function getHeadersNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('headers');

        $node
            ->fixXmlConfig('header')
            ->normalizeKeys(false)
            ->useAttributeAsKey('name')
            ->example(['X-Custom-Header' => 'Custom Value'])
            ->prototype('scalar')->end()
            ->end();

        return $node;
    }

    private function getContextNode()
    {
        //FIXME: test services
        $builder = new TreeBuilder();
        $node = $builder->root('context');

        $node
            ->fixXmlConfig('context', 'context')
            ->normalizeKeys(false)
            ->useAttributeAsKey('key')
            ->example(array('foo' => '"@bar"', 'pi' => 3.14))
            ->prototype('scalar')->end()
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
                            ->arrayNode('required_context_keys')
                            ->fixXmlConfig('required_context_key')
                                ->defaultValue([])
                                ->prototype('scalar')->end()
                            ->end()
                            ->append($this->getSenderNode())
                            ->append($this->getContextNode())
                            ->append($this->getHeadersNode())
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
