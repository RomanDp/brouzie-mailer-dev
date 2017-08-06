<?php

namespace Brouzie\Mailer\Tests\Model;

use Brouzie\Mailer\Model\Email;
use Brouzie\Mailer\Model\PredefinedEmail;
use PHPUnit\Framework\TestCase;

class PredefinedEmailTest extends TestCase
{
    /**
     * @expectedException \Brouzie\Mailer\Exception\MissingRequiredContextParametersException
     * @expectedExceptionMessage Some required parameters are missing ("user", "friend") to render email.
     */
    public function testValidateContextOnInvalidContext()
    {
        $predefinedEmail = new PredefinedEmail(new Email(), [], ['user', 'friend']);
        $predefinedEmail->validateContext([]);
    }
}
