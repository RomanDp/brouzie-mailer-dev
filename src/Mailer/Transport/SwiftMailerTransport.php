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

        $content = $email->getContent();

        if ($content) {
            $replacements = [];
            foreach ($email->getEmbeddedFiles() as $name => $embeddedFile) {
                $swiftEmbeddedFile = new \Swift_EmbeddedFile(
                    $embeddedFile->getContent(),
                    $embeddedFile->getFilename(),
                    $embeddedFile->getContentType()
                );
                $replacements[$email->embedFile($name)] = $message->embed($swiftEmbeddedFile);
            }

            if ($replacements) {
                $content = strtr($content, $replacements);
            }

            $message
                ->setBody($content, 'text/html')
                ->addPart($email->getPlainTextContent(), 'text/plain');
        } else {
            $message->setBody($email->getPlainTextContent());
        }

        foreach ($email->getAttachments() as $attachment) {
            $swiftAttachment = new \Swift_Attachment(
                $attachment->getContent(),
                $attachment->getFilename(),
                $attachment->getContentType()
            );
            $message->attach($swiftAttachment);
        }

        //TODO: separate headers by type?
        foreach ($email->getHeaders() as $headerName => $headerValue) {
            $message->getHeaders()->addTextHeader($headerName, $headerValue);
        }

//        if ($replyTo) {
//            $message->setReplyTo($replyTo);
//        }

        //TODO: handle failed recipients
        $this->mailer->send($message);
    }
}
