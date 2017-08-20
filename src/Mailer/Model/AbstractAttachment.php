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

    /**
     * @param resource $stream
     * @param string|null $filename
     * @param string|null $contentType
     *
     * @return static
     */
    public static function fromStream($stream, string $filename = null, string $contentType = null)
    {
        $content = stream_get_contents($stream);

        if (null === $contentType) {
            $finfo = new \finfo(FILEINFO_MIME);
            $contentType = $finfo->buffer($content);
            list($contentType) = explode(';', $contentType);
        }

        if (null === $filename) {
            $filename = basename(stream_get_meta_data($stream)['uri']);
        }

        return new static($content, $filename, $contentType);
    }

    public static function fromPath(string $path, string $filename = null, string $contentType = null)
    {
        return new static(
            file_get_contents($path),
            $filename ?: basename($path),
            $contentType ?: mime_content_type($path)
        );
    }
}
