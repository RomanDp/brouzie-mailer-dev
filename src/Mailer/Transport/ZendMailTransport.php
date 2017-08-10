<?php

namespace Brouzie\Mailer\Transport;

use Brouzie\Mailer\Model\Email;
use Zend\Mail\Message;
use Zend\Mail\Transport as ZendTransport;

class ZendMailTransport implements Transport
{
    private $zendTransport;

    public function __construct(ZendTransport $zendTransport)
    {
        $this->zendTransport = $zendTransport;
    }

    public function send(Email $email): void
    {
        //FIXME: implement transport
        $message = new Message();
        $message->addTo('matthew@example.org');
        $message->addFrom('ralph@example.org');
        $message->setSubject('Greetings and Salutations!');
        $message->setBody("Sorry, I'm going to be late today!");
    }
}
