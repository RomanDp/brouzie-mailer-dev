<?php

namespace Brouzie\Mailer\Renderer;

use Brouzie\Mailer\Exception\RendererNotFoundException;
use Brouzie\Mailer\Model\Email;

interface Renderer
{
    /**
     * @param Email $email
     * @param array $context
     *
     * @throws RendererNotFoundException
     */
    public function render(Email $email, array $context = []): void;

    public function supports(Email $email): bool;
}
