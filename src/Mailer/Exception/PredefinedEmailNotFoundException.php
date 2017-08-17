<?php

namespace Brouzie\Mailer\Exception;

class PredefinedEmailNotFoundException extends InvalidArgumentException
{
    public static function create(string $requestedName, array $availableNames = [], \Exception $previous = null)
    {
        $msg = sprintf('Predefined email "%s" not exists.', $requestedName);

        if (count($availableNames)) {
            $msg .= sprintf(' Available email names: "%s".', implode('", "', array_keys($availableNames)));
        }

        return new self($msg, null, $previous);
    }
}
