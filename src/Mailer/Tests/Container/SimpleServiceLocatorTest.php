<?php

namespace Brouzie\Mailer\Tests\Container;

use Brouzie\Mailer\Container\SimpleServiceLocator;
use Brouzie\Mailer\Exception\ServiceNotFoundException;
use PHPUnit\Framework\TestCase;

class SimpleServiceLocatorTest extends TestCase
{
    public function testHas()
    {
        $serviceLocator = new SimpleServiceLocator(['service_a' => new \stdClass(), 'service_b' => new \stdClass()]);

        $this->assertTrue($serviceLocator->has('service_a'));
        $this->assertFalse($serviceLocator->has('service_c'));
    }

    public function testGet()
    {
        $serviceA = new \stdClass();
        $serviceLocator = new SimpleServiceLocator(['service_a' => $serviceA, 'service_b' => new \stdClass()]);

        $this->assertSame($serviceA, $serviceLocator->get('service_a'));
    }

    public function testGetNotFoundService()
    {
        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionMessage('You have requested a non-existent service "service_c". Did you mean one of these: "service_a", "service_b"?');

        $serviceA = new \stdClass();
        $serviceLocator = new SimpleServiceLocator(['service_a' => $serviceA, 'service_b' => new \stdClass()]);

        try {
            $serviceLocator->get('service_c');
        } catch (ServiceNotFoundException $e) {
            $this->assertSame(['service_a', 'service_b'], $e->getAlternatives());

            throw $e;
        }
    }
}
