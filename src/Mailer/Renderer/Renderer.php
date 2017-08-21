<?php

namespace Brouzie\Mailer\Renderer;

use Brouzie\Mailer\Exception\IncompleteEmailException;
use Brouzie\Mailer\Model\Email;

interface Renderer
{
    /**
     * @param Email $email
     * @param array $context
     *
     * @throws IncompleteEmailException If email missing required part.
     */
    public function render(Email $email, array $context = []): void;

    public function supports(Email $email): bool;
}
