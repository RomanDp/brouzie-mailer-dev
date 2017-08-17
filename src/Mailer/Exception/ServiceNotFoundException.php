<?php

namespace Brouzie\Mailer\Exception;

use Psr\Container\NotFoundExceptionInterface;

class ServiceNotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
    private $id;
    private $alternatives;

    public function __construct($id, \Exception $previous = null, array $alternatives = [])
    {
        $msg = sprintf('You have requested a non-existent service "%s".', $id);

        if ($alternatives) {
            if (1 == count($alternatives)) {
                $msg .= ' Did you mean this: "';
            } else {
                $msg .= ' Did you mean one of these: "';
            }
            $msg .= implode('", "', $alternatives).'"?';
        }

        parent::__construct($msg, 0, $previous);

        $this->id = $id;
        $this->alternatives = $alternatives;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAlternatives(): array
    {
        return $this->alternatives;
    }
}
