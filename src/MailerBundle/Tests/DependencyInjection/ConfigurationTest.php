<?php

namespace Brouzie\MailerBundle\Tests\DependencyInjection;

use Brouzie\MailerBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    /**
     * @dataProvider senderDataProvider
     */
    public function testSenderNodeNormalization(array $userConfig, array $normalizedConfig)
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(
            new Configuration(),
            [$this->getDefaultConfiguration(), $userConfig]
        );

        $this->assertArraySubset($normalizedConfig, $config);
    }

    /**
     * @dataProvider invalidSenderProvider
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessageRegExp /sender/
     */
    public function testSenderNodeRequired(array $userConfig)
    {
        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), [$userConfig]);
    }

    /**
     * @dataProvider transportsProvider
     */
    public function testFirstTransportSetsAsDefault(array $userConfig, string $defaultTransport)
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [$userConfig]);

        $this->assertSame($defaultTransport, $config['default_transport']);
    }

    /**
     * @dataProvider invalidEmailTypesProvider
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessageRegExp  /Please define one and only one of twig\/twig_blocks\/service keys\./
     */
    public function testEmailsTypesRequired(array $userConfig)
    {
        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), [$this->getDefaultConfiguration(), $userConfig]);
    }

    public function senderDataProvider()
    {
        return [
            [
                ['sender' => 'info@site.com'],
                ['sender' => ['address' => 'info@site.com', 'name' => null]],
            ],
            [
                ['sender' => ['info@site.com' => 'Site Notifications System']],
                ['sender' => ['address' => 'info@site.com', 'name' => 'Site Notifications System']],
            ],
            [
                ['sender' => ['address' => 'info@site.com', 'name' => 'Site Notifications System']],
                ['sender' => ['address' => 'info@site.com', 'name' => 'Site Notifications System']],
            ],
        ];
    }

    public function invalidSenderProvider()
    {
        return [
            [
                [],
            ],
            [
                ['sender' => null],
            ],
            [
                ['sender' => ['address' => null]],
            ],
        ];
    }

    public function transportsProvider()
    {
        return [
            [
                [
                    'sender' => 'info@site.com',
                    'transports' => [
                        'default' => ['type' => 'swiftmailer', 'service' => 'swiftmailer.default'],
                        'delayed' => ['type' => 'swiftmailer', 'service' => 'swiftmailer.delayed'],
                    ],
                ],
                'default',
            ],
            [
                [
                    'sender' => 'info@site.com',
                    'default_transport' => 'delayed',
                    'transports' => [
                        'default' => ['type' => 'swiftmailer', 'service' => 'swiftmailer.default'],
                        'delayed' => ['type' => 'swiftmailer', 'service' => 'swiftmailer.delayed'],
                    ],
                ],
                'delayed',
            ],
        ];
    }

    public function invalidEmailTypesProvider()
    {
        return [
            [
                [
                    'emails' => [
                        'one' => [],
                    ],
                ],
            ],
            [
                [
                    'emails' => [
                        'one' => [],
                        'two' => ['twig' => '@AppBundle/path/to/template.html.twig', 'service' => 'service.id'],
                    ],
                ],
            ],
        ];
    }

    private function getDefaultConfiguration()
    {
        return [
            'sender' => 'info@site.com',
            'transports' => ['default' => ['type' => 'swiftmailer', 'service' => 'swiftmailer.default']],
        ];
    }
}
