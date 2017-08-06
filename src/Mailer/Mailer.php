<?php

namespace Brouzie\Mailer;

use Brouzie\Mailer\Exception\PredefinedEmailNotFoundException;
use Brouzie\Mailer\Exception\TransportNotFoundException;
use Brouzie\Mailer\Model\Email;
use Brouzie\Mailer\Model\PredefinedEmail;
use Brouzie\Mailer\Renderer\Renderer;
use Brouzie\Mailer\Transport\Transport;

class Mailer
{
    private $renderer;

    private $transports;

    private $defaultTransport;

    private $namedEmails;

    /**
     * @param Renderer $renderer
     * @param Transport[] $transports
     * @param string $defaultTransport
     * @param PredefinedEmail[] $namedEmails
     */
    public function __construct(
        Renderer $renderer,
        array $transports,
        string $defaultTransport = 'default',
        array $namedEmails = []
    ) {
        $this->renderer = $renderer;
        $this->transports = $transports;
        $this->defaultTransport = $defaultTransport;
        if (!isset($this->transports[$defaultTransport])) {
            throw new TransportNotFoundException(sprintf('Missing default transport "%s".', $defaultTransport));
        }
        $this->namedEmails = $namedEmails;
    }

    public function sendEmail(Email $email, array $context = [], $transport = null)
    {
        $this->renderer->render($email, $context);
        $this->getTransport($transport)->send($email);
    }

    public function sendNamedEmail($name, array $context = [], callable $configurator = null)
    {
        if (!isset($this->namedEmails[$name])) {
            throw PredefinedEmailNotFoundException::create($name, array_keys($this->namedEmails));
        }

        $predefinedEmail = $this->namedEmails[$name];
        $context = array_replace($predefinedEmail->getDefaultContext(), $context);
        $predefinedEmail->validateContext($context);

        if (null !== $configurator) {
            $configurator($predefinedEmail->getEmail(), $context);
        }

        $this->sendEmail($predefinedEmail->getEmail(), $context, $predefinedEmail->getTransport());
    }

    private function getTransport($transport)
    {
        $transport = $transport ?: $this->defaultTransport;

        if (!isset($this->transports[$transport])) {
            throw TransportNotFoundException::create($transport, array_keys($this->transports));
        }

        return $this->transports[$transport];
    }
}
