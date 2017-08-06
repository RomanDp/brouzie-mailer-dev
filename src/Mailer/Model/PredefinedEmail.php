<?php

namespace Brouzie\Mailer\Model;

use Brouzie\Mailer\Exception\MissingRequiredContextParametersException;

class PredefinedEmail
{
    private $email;

    private $defaultContext;

    private $requiredContextKeys;

    private $transport;

    public function __construct(
        Email $email,
        array $defaultContext = [],
        array $requiredContextKeys = [],
        string $transport = null
    ) {
        $this->email = $email;
        $this->defaultContext = $defaultContext;
        $this->requiredContextKeys = $requiredContextKeys;
        $this->transport = $transport;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getDefaultContext(): array
    {
        return $this->defaultContext;
    }

    public function validateContext(array $context)
    {
        if ($diff = array_diff($this->requiredContextKeys, array_keys($context))) {
            throw MissingRequiredContextParametersException::create($diff);
        }
    }

    public function getTransport(): ?string
    {
        return $this->transport;
    }
}
