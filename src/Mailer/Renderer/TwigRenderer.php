<?php

namespace Brouzie\Mailer\Renderer;

use Brouzie\Mailer\Exception\IncompleteEmailException;
use Brouzie\Mailer\Model\Email;
use Brouzie\Mailer\Model\Twig\TwigEmail;
use Twig\Environment;

class TwigRenderer implements Renderer
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render(Email $email, array $context = []): void
    {
        /** @var TwigEmail $email */
        $template = $this->twig->load($email->getTemplate());

        if ($template->hasBlock(TwigEmail::BLOCK_SUBJECT)) {
            $email->setSubject($template->renderBlock(TwigEmail::BLOCK_SUBJECT, $context));
        }

        if ($template->hasBlock(TwigEmail::BLOCK_CONTENT)) {
            $email->setContent($template->renderBlock(TwigEmail::BLOCK_CONTENT, $context));
        }

        if ($template->hasBlock(TwigEmail::BLOCK_PLAIN_TEXT_CONTENT)) {
            $email->setPlainTextContent($template->renderBlock(TwigEmail::BLOCK_PLAIN_TEXT_CONTENT, $context));
        }

        if (!$email->getContent() && !$email->getPlainTextContent()) {
            throw new IncompleteEmailException('Email requires content or plain text content.');
        }

        if ($template->hasBlock(TwigEmail::BLOCK_HEADERS)) {
            $headers = $template->renderBlock(TwigEmail::BLOCK_HEADERS, $context);
            $headers = http_parse_headers($headers);
            $email->replaceHeaders($headers);
        }
    }

    public function supports(Email $email): bool
    {
        return $email instanceof TwigEmail;
    }
}
