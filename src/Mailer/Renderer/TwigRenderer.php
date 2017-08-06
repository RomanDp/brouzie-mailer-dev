<?php

namespace Brouzie\Mailer\Renderer;

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
        $context['email'] = $this;

        if ($template->hasBlock(TwigEmail::BLOCK_SUBJECT)) {
            $email->setSubject($template->renderBlock(TwigEmail::BLOCK_SUBJECT, $context));
        }

        if ($template->hasBlock(TwigEmail::BLOCK_BODY)) {
            $email->setContent($template->renderBlock(TwigEmail::BLOCK_BODY, $context));
        }

        if ($template->hasBlock(TwigEmail::BLOCK_PLAIN_BODY)) {
            $email->setPlainTextContent($template->renderBlock(TwigEmail::BLOCK_PLAIN_BODY, $context));
        }
    }

    public function supports(Email $email): bool
    {
        return $email instanceof TwigEmail;
    }
}
