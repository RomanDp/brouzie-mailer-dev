<?php

namespace Brouzie\Mailer\Model;

class Email
{
    //FIXME: isRendered, sendStatus?

    private $subject;

    private $content;

    private $plainTextContent;

    private $sender;

    /**
     * @var Address[]
     */
    private $recipients = [];

    /**
     * @var Attachment[]
     */
    private $attachments = [];

    private $headers = [];

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject)
    {
        $this->subject = $subject;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content)
    {
        $this->content = $content;
    }

    public function getPlainTextContent(): ?string
    {
        return $this->plainTextContent;
    }

    public function setPlainTextContent(?string $plainTextContent)
    {
        $this->plainTextContent = $plainTextContent;
    }

    public function setSender(Address $sender)
    {
        $this->sender = $sender;
    }

    public function getSender(): ?Address
    {
        $this->sender;
    }

    public function addRecipient(Address $recipient)
    {
        $this->recipients[] = $recipient;
    }

    /**
     * @return Address[]
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @return Attachment[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function addAttachment(Attachment $attachment)
    {
        $this->attachments[] = $attachment;
    }

    public function getHeaders()
    {
        //FIXME: properly implement
        return $this->headers;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }
}
