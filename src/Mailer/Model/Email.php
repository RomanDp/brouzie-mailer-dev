<?php

namespace Brouzie\Mailer\Model;

use Brouzie\Mailer\Exception\InvalidArgumentException;

class Email
{
    //FIXME: isRendered, sendStatus?

    const EMBED_PLACEHOLDER = '<!--brouzie_mailer_embed[%s]-->';

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

    /**
     * @var EmbeddedFile[]
     */
    private $embeddedFiles = [];

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
        return $this->sender;
    }

    //TODO: add support of the cc, bcc
    public function addRecipient(Address $recipient)
    {
        $this->recipients[] = $recipient;
    }

    /**
     * @param iterable|Address[] $recipients
     */
    public function addRecipients(iterable $recipients)
    {
        foreach ($recipients as $recipient) {
            $this->addRecipient($recipient);
        }
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

    public function addEmbeddedFile(EmbeddedFile $embeddedFile, string $name = null)
    {
        //TODO: properly handle already exists
        $this->embeddedFiles[$name ?: $embeddedFile->getFilename()] = $embeddedFile;
    }

    /**
     * @return EmbeddedFile[]
     */
    public function getEmbeddedFiles(): array
    {
        return $this->embeddedFiles;
    }

    public function embedFile(string $name): string
    {
        if (!isset($this->embeddedFiles[$name])) {
            throw new InvalidArgumentException(sprintf('File "%s" not embedded.', $name));
        }

        return sprintf(self::EMBED_PLACEHOLDER, $name);
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function addHeaders(array $headers)
    {
        $this->headers = array_replace($headers, $this->headers);
    }

    public function replaceHeaders(array $headers)
    {
        $this->headers = array_replace($this->headers, $headers);
    }
}
