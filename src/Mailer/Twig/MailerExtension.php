<?php

namespace Brouzie\Mailer\Twig;

use Brouzie\Mailer\Exception\BadMethodCallException;
use Brouzie\Mailer\Model\Email;
use Brouzie\Mailer\Model\EmbeddedFile;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MailerExtension extends AbstractExtension
{
    private $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'brouzie_mailer_embed_file',
                [$this, 'embedFile'],
                ['needs_context' => true, 'is_safe' => ['html']]
            ),
        ];
    }

    public function embedFile(array $context, string $path, string $filename = null): string
    {
        if (!isset($context['_email']) || !$context['_email'] instanceof Email) {
            throw new BadMethodCallException('Expected call this function from email template.');
        }

        /** @var Email $email */
        $email = $context['_email'];

        $embeddedFile = EmbeddedFile::fromPath($this->basePath.'/'.$path, $filename);
        $email->addEmbeddedFile($embeddedFile);

        return $email->embedFile($embeddedFile->getFilename());
    }
}
