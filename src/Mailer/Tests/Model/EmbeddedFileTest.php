<?php

namespace Brouzie\Mailer\Tests\Model;

use Brouzie\Mailer\Model\EmbeddedFile;
use PHPUnit\Framework\TestCase;

class EmbeddedFileTest extends TestCase
{
    const FIXTURE = __DIR__.'/../Fixtures/1px-transparent.gif';

    public function testCreateFromPath()
    {
        $embeddedFile = EmbeddedFile::fromPath(self::FIXTURE);

        $this->assertInstanceOf(EmbeddedFile::class, $embeddedFile);
        $this->assertSame('1px-transparent.gif', $embeddedFile->getFilename());
        $this->assertSame('image/gif', $embeddedFile->getContentType());
        $this->assertStringEqualsFile(self::FIXTURE, $embeddedFile->getContent());
    }

    public function testCreateFromStream()
    {
        $stream = fopen(self::FIXTURE, 'rb');
        $embeddedFile = EmbeddedFile::fromStream($stream);

        $this->assertInstanceOf(EmbeddedFile::class, $embeddedFile);
        $this->assertSame('1px-transparent.gif', $embeddedFile->getFilename());
        $this->assertSame('image/gif', $embeddedFile->getContentType());
        $this->assertStringEqualsFile(self::FIXTURE, $embeddedFile->getContent());
    }
}
