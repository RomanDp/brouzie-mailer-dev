<?php

namespace Brouzie\Mailer\Exception;

class MissingRequiredContextParametersException extends InvalidArgumentException
{
    public static function create(array $missingParameters)
    {
        return new self(
            sprintf('Some required parameters are missing ("%s") to render email.', implode('", "', $missingParameters))
        );
    }
}
