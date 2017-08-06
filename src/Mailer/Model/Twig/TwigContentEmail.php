<?php

namespace Brouzie\Mailer\Model\Twig;

use Brouzie\Mailer\Model\Email;

class TwigContentEmail extends Email
{
    private $templates;

    /**
     * @param string[] $templates
     */
    public function __construct(array $templates)
    {
        $this->templates = $templates;
    }

    public function getTemplate($block)
    {
        return isset($this->templates[$block]) ? $this->templates[$block] : null;
    }
}
