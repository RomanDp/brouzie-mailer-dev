<?php

namespace Brouzie\Mailer\Renderer;

use Brouzie\Mailer\Model\Email;
use Brouzie\Mailer\Model\Twig\TwigContentEmail;
use Brouzie\Mailer\Model\Twig\TwigEmail;
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
        $context['email'] = $this;

        if ($template = $email->getTemplate(TwigEmail::BLOCK_SUBJECT)) {
            $email->setSubject($this->twig->createTemplate($template)->render($context));
        }

        if ($template = $email->getTemplate(TwigEmail::BLOCK_BODY)) {
            $email->setContent($this->twig->createTemplate($template)->render($context));
        }

        if ($template = $email->getTemplate(TwigEmail::BLOCK_PLAIN_BODY)) {
            $email->setPlainTextContent($this->twig->createTemplate($template)->render($context));
        }
    }

    public function supports(Email $email): bool
    {
        return $email instanceof TwigContentEmail;
    }
}
