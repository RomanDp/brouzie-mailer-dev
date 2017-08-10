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
        $config = $processor->processConfiguration(new Configuration(), [$userConfig]);

        $this->assertEquals($normalizedConfig, $config);
    }

    /**
     * @dataProvider invalidSenderProvider
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testSenderNodeRequired(array $userConfig)
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [$userConfig]);
    }

    public function senderDataProvider()
    {
        return [
            [
                ['sender' => 'info@site.com'],
                ['sender' => ['address' => 'info@site.com']],
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
}
