<?php

namespace Brouzie\Mailer\Exception;

class TransportNotFoundException extends InvalidArgumentException
{
    public static function create(string $requestedTransport, array $availableTransports)
    {
        return new self(
            sprintf(
                'Transport "%s" not exists. Available transports: "%s".',
                $requestedTransport,
                implode('", "', array_keys($availableTransports))
            )
        );
    }
}
