<?php

namespace Brouzie\Mailer\Transport;

use Brouzie\Mailer\Model\Email;

class SwiftMailerTransport implements Transport
{
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(Email $email): void
    {
        /** @var \Swift_Message $message */
        $message = $this->mailer->createMessage();
        $message
            ->setSubject($email->getSubject())
            ->setFrom($email->getSender()->getAddress(), $email->getSender()->getName());

        foreach ($email->getRecipients() as $recipient) {
            $message->addTo($recipient->getAddress(), $recipient->getName());
        }

//        if ($replyTo) {
//            $message->setReplyTo($replyTo);
//        }

        if ($email->getContent()) {
            $message
                ->setBody($email->getContent(), 'text/html')
                ->addPart($email->getPlainTextContent(), 'text/plain');
        } else {
            $message->setBody($email->getPlainTextContent());
        }

        //FIXME: headers, attachments

        //TODO: handle failed recipients
        $this->mailer->send($message);
    }
}
