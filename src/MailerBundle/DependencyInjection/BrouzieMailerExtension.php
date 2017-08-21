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

        $loader->load('mailer.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('brouzie_mailer.default_sender', [$config['sender']['address'], $config['sender']['name']]);
        $container->setParameter('brouzie_mailer.default_transport', $config['default_transport']);
        $container->setParameter('brouzie_mailer.default_context', $config['context']);
        $container->setParameter('brouzie_mailer.default_headers', $config['headers']);

        $this->configureTransports($config['transports'], $container);
        $this->configureEmails($config['emails'], $container);
    }

    private function configureTransports(array $transports, ContainerBuilder $container)
    {
        $transportReferences = [];
        foreach ($transports as $name => $transportConfig) {
            switch ($transportConfig['type']) {
                case 'swiftmailer':
                    $definitionDecorator = $this->createChildDefinition('brouzie_mailer.transport.swiftmailer.abstract');
                    $id = sprintf('brouzie_mailer.transport.swiftmailer.%s', $name);
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
        $transportLocator = $container->getDefinition('brouzie_mailer.transport_locator');
        $transportLocator->replaceArgument(0, $transportReferences);
        if (class_exists(ServiceLocator::class)) {
            $transportLocator->setClass(ServiceLocator::class);
        }
    }

    private function configureEmails(array $emails, ContainerBuilder $container)
    {
//        dump($emails);exit;

        $emailReferences = [];
        foreach ($emails as $name => $emailConfig) {
            if (!empty($emailConfig['twig'])) {
                $definitionDecorator = $this->createChildDefinition('brouzie_mailer.emails.twig_email.abstract');
                $emailId = sprintf('brouzie_mailer.emails.twig_email.%s', $name);
                $container
                    ->setDefinition($emailId, $definitionDecorator)
                    ->replaceArgument(0, $emailConfig['twig']);
            } elseif (!empty($emailConfig['twig_blocks'])) {
                $definitionDecorator = $this->createChildDefinition('brouzie_mailer.emails.twig_content_email.abstract');
                $emailId = sprintf('brouzie_mailer.emails.twig_content_email.%s', $name);
                $container
                    ->setDefinition($emailId, $definitionDecorator)
                    ->replaceArgument(0, $emailConfig['twig_blocks']);
            } elseif (!empty($emailConfig['service'])) {
                $emailId = $emailConfig['service'];
                //FIXME: add logic
                continue;
            } else {
                continue;
            }

            $definitionDecorator = $this->createChildDefinition('brouzie_mailer.emails.predefined_email.abstract');
            $id = sprintf('brouzie_mailer.emails.email.%s', $name);
            $container
                ->setDefinition($id, $definitionDecorator)
                ->replaceArgument(0, new Reference($emailId))
                ->replaceArgument(1, $emailConfig['context'])
                ->replaceArgument(2, $emailConfig['required_context_keys'])
                ->replaceArgument(3, $emailConfig['transport']);

            $emailReferences[$name] = new Reference($id);
        }

        $transportLocator = $container->getDefinition('brouzie_mailer.predefined_email_locator');
        $transportLocator->replaceArgument(0, $emailReferences);
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
