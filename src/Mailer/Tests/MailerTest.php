<?php

namespace Brouzie\Mailer\Tests;

use Brouzie\Mailer\Mailer;
use Brouzie\Mailer\Model\Address;
use PHPUnit\Framework\TestCase;

class MailerTest extends TestCase
{
    /**
     * @dataProvider recipientsProvider
     */
    public function testNormalizeRecipients($input, $recipients)
    {
        $this->assertEquals($recipients, Mailer::normalizeRecipients($input));
    }

    public function recipientsProvider()
    {
        return [
            ['email@site.com', [new Address('email@site.com')]],
            [['email@site.com' => 'Target Recipient'], [new Address('email@site.com', 'Target Recipient')]],
            [new Address('email@site.com'), [new Address('email@site.com')]],
            [[new Address('email@site.com')], [new Address('email@site.com')]],
            [['email@site.com', 'email2@site.com'], [new Address('email@site.com'), new Address('email2@site.com')]],
            [
                ['email@site.com', new Address('email2@site.com')],
                [new Address('email@site.com'), new Address('email2@site.com')]
            ],
            [
                ['email@site.com' => 'Target Recipient', new Address('email2@site.com')],
                [new Address('email@site.com', 'Target Recipient'), new Address('email2@site.com')]
            ],
        ];
    }
}
