<?php

namespace Brouzie\Mailer\Renderer;

use Brouzie\Mailer\Model\Email;

interface Renderer
{
    /**
     * @param Email $email
     * @param array $context
     *
     * @throws \InvalidArgumentException
     */
    public function render(Email $email, array $context = []): void;

    public function supports(Email $email): bool;
}
