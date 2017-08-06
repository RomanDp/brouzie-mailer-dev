<?php

namespace Brouzie\Mailer\Model;

class Address
{
    private $address;

    private $name;

    /**
     * @param string|array $address
     * @param string $name
     */
    public function __construct($address, string $name = null)
    {
        if (is_array($address)) {
            $name = current($address);
            $address = key($address);
        }

        $this->address = $address;
        $this->name = $name;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address)
    {
        $this->address = $address;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name)
    {
        $this->name = $name;
    }
}
