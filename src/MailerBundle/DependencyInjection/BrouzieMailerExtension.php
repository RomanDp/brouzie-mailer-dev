<?php

namespace Brouzie\MailerBundle\DependencyInjection;

use Brouzie\Mailer\Exception\InvalidArgumentException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;

class BrouzieMailerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));

        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $mailer = $container->getDefinition('brouzie.mailer');


        $this->configureTransports($config['transports'], $container);
    }

    private function configureTransports(array $transports, ContainerBuilder $container)
    {
        $transportReferences = [];
        foreach ($transports as $name => $transportConfig) {
            switch ($transportConfig['type']) {
                case 'swiftmailer':
                    $definitionDecorator = $this->createChildDefinition('swiftmailer.mailer.abstract');
                    $id = sprintf('brouzie.mailer.transport.swiftmailer.%s', $name);
                    $container
                        ->setDefinition($id, $definitionDecorator)
                        ->replaceArgument(0, new Reference($transportConfig['service']));

                    $transportReference = new Reference($id);
                    break;

                case 'service':
                    $transportReference = new Reference($transportConfig['service']);
                    break;

                default:
                    throw new InvalidArgumentException('Unknown transport type.');
            }

            $transportReferences[$name] = $transportReference;
        }

        // http://symfony.com/doc/current/service_container/service_locators.html
        $transportLocator = $container->getDefinition('brouzie.mailer.transport_locator');
        $transportLocator->replaceArgument(0, $transportReferences);
        if (class_exists(ServiceLocator::class)) {
            $transportLocator->setClass(ServiceLocator::class);
        }
    }

    private function createChildDefinition($id)
    {
        if (class_exists(ChildDefinition::class)) {
            return new ChildDefinition($id);
        }

        return new DefinitionDecorator($id);
    }
}
