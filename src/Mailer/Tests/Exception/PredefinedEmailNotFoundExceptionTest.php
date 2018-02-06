<?php

namespace Brouzie\Mailer\Tests\Exception;

use Brouzie\Mailer\Exception\PredefinedEmailNotFoundException;
use PHPUnit\Framework\TestCase;

class PredefinedEmailNotFoundExceptionTest extends TestCase
{
    public function testCreate()
    {
        $this->expectException(PredefinedEmailNotFoundException::class);
        throw PredefinedEmailNotFoundException::create('name', []);
    }

    public function testCreateWithAvailableNames()
    {
        $e = PredefinedEmailNotFoundException::create('name', ['first', 'second']);
        $this->assertRegExp('/"first", "second"/i', $e->getMessage());

    }
}