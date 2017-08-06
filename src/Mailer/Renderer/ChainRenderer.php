<?php

namespace Brouzie\MailerBundle\src\Mailer\Renderer;

use Brouzie\Mailer\Exception\RendererNotFoundException;
use Brouzie\Mailer\Model\Email;
use Brouzie\Mailer\Renderer\Renderer;

class ChainRenderer implements Renderer
{
    private $renderers;

    /**
     * @param Renderer[] $renderers
     */
    public function __construct(array $renderers)
    {
        $this->renderers = $renderers;
    }

    public function render(Email $email, array $context = []): void
    {
        foreach ($this->renderers as $renderer) {
            if ($renderer->supports($email)) {
                $renderer->render($email, $context);

                return;
            }
        }

        throw new RendererNotFoundException('No renderer found for email.');
    }

    public function supports(Email $email): bool
    {
        foreach ($this->renderers as $renderer) {
            if ($renderer->supports($email)) {
                return true;
            }
        }

        return false;
    }
}
