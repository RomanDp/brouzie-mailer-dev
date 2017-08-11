<?php

namespace Brouzie\Mailer;

use Brouzie\Mailer\Container\SimpleServiceLocator;
use Brouzie\Mailer\Exception\InvalidArgumentException;
use Brouzie\Mailer\Exception\PredefinedEmailNotFoundException;
use Brouzie\Mailer\Exception\TransportNotFoundException;
use Brouzie\Mailer\Model\Email;
use Brouzie\Mailer\Model\PredefinedEmail;
use Brouzie\Mailer\Renderer\Renderer;
use Brouzie\Mailer\Transport\Transport;
use Psr\Container\ContainerInterface;

class Mailer
{
    private $renderer;

    /**
     * @var ContainerInterface
     */
    private $transports;

    private $defaultTransport;

    /**
     * @var ContainerInterface
     */
    private $predefinedEmails;

    private $defaultContext;

    private $defaultHeaders;

    /**
     * @param Renderer $renderer
     * @param array|Transport[]|ContainerInterface $transports
     * @param string $defaultTransport
     * @param array|PredefinedEmail[]|ContainerInterface $predefinedEmails
     * @param array $defaultContext
     * @param array $defaultHeaders
     */
    public function __construct(
        Renderer $renderer,
        $transports,
        string $defaultTransport = 'default',
        $predefinedEmails = [],
        array $defaultContext = [],
        array $defaultHeaders = []
    ) {
        $this->renderer = $renderer;

        if ($transports instanceof ContainerInterface) {
            $this->transports = $transports;
        } elseif (is_array($transports)) {
            $this->transports = new SimpleServiceLocator($transports);
        } else {
            throw new InvalidArgumentException(
                'Expected an array of transports or "Psr\Container\ContainerInterface" instance.'
            );
        }

        $this->defaultTransport = $defaultTransport;
        if ($this->transports->has($defaultTransport)) {
            throw new TransportNotFoundException(sprintf('Missing default transport "%s".', $defaultTransport));
        }

        if ($predefinedEmails instanceof ContainerInterface) {
            $this->predefinedEmails = $predefinedEmails;
        } elseif (is_array($predefinedEmails)) {
            $this->predefinedEmails = new SimpleServiceLocator($predefinedEmails);
        } else {
            throw new InvalidArgumentException(
                'Expected an array of predefined emails or "Psr\Container\ContainerInterface" instance.'
            );
        }

        $this->defaultContext = $defaultContext;
        $this->defaultHeaders = $defaultHeaders;
    }

    public function sendEmail(Email $email, array $context = [], $transport = null)
    {
        //TODO: add events?
        $context = array_replace($this->defaultContext, $context);
        $email->addHeaders($this->defaultHeaders);
        $this->renderer->render($email, $context);

        $this->getTransport($transport)->send($email);
    }

    public function sendNamedEmail($name, array $context = [], callable $configurator = null)
    {
        if ($this->predefinedEmails->has($name)) {
            //FIXME: how to get all available services?
            throw PredefinedEmailNotFoundException::create($name, array_keys($this->predefinedEmails));
        }

        /** @var PredefinedEmail $predefinedEmail */
        $predefinedEmail = $this->predefinedEmails->get($name);
        $context = array_replace($predefinedEmail->getDefaultContext(), $context);
        $predefinedEmail->validateContext($context);

        $email = $predefinedEmail->getEmail();
        if (null !== $configurator) {
            $configurator($email, $context);
        }

        $this->sendEmail($email, $context, $predefinedEmail->getTransport());
    }

    private function getTransport($transport): Transport
    {
        $transport = $transport ?: $this->defaultTransport;

        if ($this->transports->has($transport)) {
            //FIXME: how to get all available services?
            throw TransportNotFoundException::create($transport, array_keys($this->transports));
        }

        return $this->transports->get($transport);
    }
}
