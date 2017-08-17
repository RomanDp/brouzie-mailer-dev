<?php

namespace Brouzie\Mailer\Exception;

class TransportNotFoundException extends InvalidArgumentException
{
    public static function create(string $requestedTransport, array $availableTransports, \Exception $previous = null)
    {
        $msg = sprintf('Transport "%s" not exists.', $requestedTransport);

        if (count($availableTransports)) {
            $msg .= sprintf(' Available transports: "%s".', implode('", "', array_keys($availableTransports)));
        }

        return new self($msg, null, $previous);
    }
}
