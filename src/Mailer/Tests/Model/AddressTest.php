<?php

namespace Brouzie\Mailer\Tests\Model;

use Brouzie\Mailer\Model\Address;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    /**
     * @dataProvider addressesProvider
     */
    public function testAddressCanonized($address, $name, $expectedAddress, $expectedName)
    {
        $model = new Address($address, $name);

        $this->assertSame($model->getAddress(), $expectedAddress);
        $this->assertSame($model->getName(), $expectedName);
    }

    public function addressesProvider()
    {
        return [
            ['aaa@aaa.com', null, 'aaa@aaa.com', null],
            ['aaa@aaa.com', 'Name', 'aaa@aaa.com', 'Name'],
            [['aaa@aaa.com' => null], null, 'aaa@aaa.com', null],
            [['aaa@aaa.com' => 'Name'], null, 'aaa@aaa.com', 'Name'],
            [['aaa@aaa.com' => 'Name'], 'Other Name', 'aaa@aaa.com', 'Name'],
        ];
    }
}
