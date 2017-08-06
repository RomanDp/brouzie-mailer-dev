<?php

namespace Brouzie\Mailer\Transport;

use Brouzie\Mailer\Model\Email;

interface Transport
{
    public function send(Email $email): void;
}
