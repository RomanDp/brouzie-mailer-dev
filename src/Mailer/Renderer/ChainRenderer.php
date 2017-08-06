<?php

namespace Brouzie\Mailer\Renderer;

use Brouzie\Mailer\Exception\RendererNotFoundException;
use Brouzie\Mailer\Model\Email;

class ChainRenderer implements Renderer
{
    private $renderers;

    /**
     * @param iterable|Renderer[] $renderers
     */
    public function __construct(iterable $renderers)
    {
        $this->renderers = $renderers;
    }

    public function render(Email $email, array $context = []): void
    {
        //TODO: who calls supports - this class or other?
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
