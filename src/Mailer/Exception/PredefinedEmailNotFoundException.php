<?php

namespace Brouzie\Mailer\Exception;

class PredefinedEmailNotFoundException extends InvalidArgumentException
{
    public static function create(string $requestedName, array $availableNames)
    {
        return new self(
            sprintf(
                'Predefined email "%s" not exists. Available email names: "%s".',
                $requestedName,
                implode('", "', array_keys($availableNames))
            )
        );
    }
}
