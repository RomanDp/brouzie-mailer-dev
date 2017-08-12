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
            ->beforeNormalization()
                ->ifTrue(function ($v) { return is_array($v) && !array_key_exists('transports', $v) && !array_key_exists('transport', $v); })
                ->then(function ($v) {
                    $transport = array();
                    foreach ($v as $key => $value) {
                        if ('default_transport' === $key) {
                            continue;
                        }
                        $transport[$key] = $v[$key];
                        unset($v[$key]);
                    }
                    $v['default_transport'] = isset($v['default_transport']) ? (string) $v['default_transport'] : 'default';
                    $v['transports'] = array($v['default_transport'] => $transport);

                    return $v;
                })
            ->end()
            ->children()
                ->append($this->getSenderNode())
                ->append($this->getContextNode())
                ->append($this->getHeadersNode())
                ->append($this->getTransportsNode())
                ->scalarNode('default_transport')->end()
                ->append($this->getTransportsNode())
                ->append($this->getEmailsNode())
            ->end();

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

    private function getTransportsNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('transports');
        //TODO: add zend support

        $node
            ->requiresAtLeastOneElement()
            ->fixXmlConfig('transport')
            ->normalizeKeys(false)
            ->useAttributeAsKey('name')
            ->prototype('array')->end()
            ->children()
                ->enumNode('type')->values(['swiftmailer', 'service'])->end()
                ->scalarNode('service')->cannotBeEmpty()->end()
            ->end()
            ->end();

        return $node;
    }

    private function getContextNode()
    {
        //TODO: add services support?
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

    private function getEmailsNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('emails');

        $node
            ->fixXmlConfig('email')
            ->useAttributeAsKey('name')
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
        ;
    }
}
