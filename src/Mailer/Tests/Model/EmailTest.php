<?php

namespace Brouzie\Mailer\Tests\Model;

use Brouzie\Mailer\Exception\InvalidArgumentException;
use Brouzie\Mailer\Model\Email;
use Brouzie\Mailer\Model\EmbeddedFile;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    /**
     * @dataProvider filesProvider
     */
    public function testAddEmbeddedFile(EmbeddedFile $embeddedFile, $name, $expectedEmbeddedFile)
    {
        $email = new Email();
        $email->addEmbeddedFile($embeddedFile, $name);
        $this->assertSame($expectedEmbeddedFile, $email->getEmbeddedFiles());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidEmbedFile()
    {
        $email = new Email();
        $email->embedFile('file');
        $this->stringContains('not embedded');
    }

    public function testEmbedFile()
    {
        $email = new Email();
        $embeddedFile = new EmbeddedFile('file_string', 'file_name', 'text/html');
        $email->addEmbeddedFile($embeddedFile);
        $email->embedFile('file_name');

    }

    public function filesProvider()
    {
        $embeddedFile = new EmbeddedFile('file_string', 'file_name', 'text/html');
        $mockEmbeddedFile = $this->createMock(EmbeddedFile::class);
        $mockEmbeddedFile->method('getFilename')->willReturn('file_name_1');


        return [
            [$embeddedFile, 'ololo', ['ololo' => $embeddedFile]],
            [$mockEmbeddedFile, null, [$mockEmbeddedFile->getFilename() => $mockEmbeddedFile]],
        ];
    }
}
