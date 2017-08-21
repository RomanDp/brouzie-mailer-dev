<?php

namespace Brouzie\Mailer\Tests\Renderer;

use Brouzie\Mailer\Model\Email;
use Brouzie\Mailer\Model\Twig\TwigContentEmail;
use Brouzie\Mailer\Model\Twig\TwigEmail;
use Brouzie\Mailer\Renderer\TwigContentRenderer;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Template;

class TwigContentRendererTest extends TestCase
{
    public function testSupports()
    {
        $twig = $this->createMock(Environment::class);
        $renderer = new TwigContentRenderer($twig);

        $this->assertTrue($renderer->supports(new TwigContentEmail([])));
        $this->assertFalse($renderer->supports(new Email()));
    }

    public function testRender()
    {
        $renderer = new TwigContentRenderer($this->getTwigMock());

        $email = new TwigContentEmail(
            [
                TwigEmail::BLOCK_SUBJECT => 'subject %foo%',
                TwigEmail::BLOCK_CONTENT => 'content <b>%foo%</b>',
                TwigEmail::BLOCK_PLAIN_TEXT_CONTENT => 'plain text content %foo%',
                TwigEmail::BLOCK_HEADERS => 'X-Header-Name: %foo%',
            ]
        );

        $this->assertNull($email->getSubject());
        $this->assertNull($email->getContent());
        $this->assertNull($email->getPlainTextContent());
        $this->assertSame([], $email->getHeaders());

        $renderer->render($email, ['%foo%' => 'foo_value']);

        $this->assertSame('subject foo_value', $email->getSubject());
        $this->assertSame('content <b>foo_value</b>', $email->getContent());
        $this->assertSame('plain text content foo_value', $email->getPlainTextContent());
        $this->assertSame(['X-Header-Name' => 'foo_value'], $email->getHeaders());
    }

    /**
     * @expectedException \Brouzie\Mailer\Exception\IncompleteEmailException
     */
    public function testRenderIncompleteEmail()
    {
        $renderer = new TwigContentRenderer($this->getTwigMock());

        $email = new TwigContentEmail(
            [
                TwigEmail::BLOCK_SUBJECT => 'subject %foo%',
            ]
        );

        $renderer->render($email, ['%foo%' => 'foo_value']);
    }

    protected function getTwigMock()
    {
        $twig = $this->createMock(Environment::class);

        $twig
            ->expects($this->any())
            ->method('createTemplate')
            ->willReturnCallback(
                function ($templateContent) {
                    $template = $this->createMock(Template::class);

                    $template
                        ->expects($this->any())
                        ->method('render')
                        ->willReturnCallback(
                            function ($context) use ($templateContent) {
                                return strtr($templateContent, $context);
                            }
                        );

                    return $template;
                }
            );

        return $twig;
    }
}
