<?php

namespace Brouzie\Mailer\Model;

abstract class AbstractAttachment
{
    private $filename;
    private $contentType;
    private $content;

    public function __construct(string $content, string $filename, string $contentType)
    {
        $this->content = $content;
        $this->filename = $filename;
        $this->contentType = $contentType;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }
}
