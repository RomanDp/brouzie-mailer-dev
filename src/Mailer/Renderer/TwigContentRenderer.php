<?php

namespace Brouzie\Mailer\Renderer;

use Brouzie\Mailer\Exception\IncompleteEmailException;
use Brouzie\Mailer\Model\Email;
use Brouzie\Mailer\Model\Twig\TwigContentEmail;
use Brouzie\Mailer\Model\Twig\TwigEmail;
use Brouzie\MailerBundle\Util\HeadersUtils;
use Twig\Environment;

class TwigContentRenderer implements Renderer
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render(Email $email, array $context = []): void
    {
        /** @var TwigContentEmail $email */
        if ($template = $email->getTemplateContent(TwigEmail::BLOCK_SUBJECT)) {
            $email->setSubject($this->twig->createTemplate($template)->render($context));
        }

        if ($template = $email->getTemplateContent(TwigEmail::BLOCK_CONTENT)) {
            $email->setContent($this->twig->createTemplate($template)->render($context));
        }

        if ($template = $email->getTemplateContent(TwigEmail::BLOCK_PLAIN_TEXT_CONTENT)) {
            $email->setPlainTextContent($this->twig->createTemplate($template)->render($context));
        }

        if (!$email->getContent() && !$email->getPlainTextContent()) {
            throw new IncompleteEmailException('Email requires content or plain text content.');
        }

        if ($template = $email->getTemplateContent(TwigEmail::BLOCK_HEADERS)) {
            $headers = $this->twig->createTemplate($template)->render($context);
            $headers = HeadersUtils::parseHeadersFromString($headers);
            $email->replaceHeaders($headers);
        }
    }

    public function supports(Email $email): bool
    {
        return $email instanceof TwigContentEmail;
    }
}
