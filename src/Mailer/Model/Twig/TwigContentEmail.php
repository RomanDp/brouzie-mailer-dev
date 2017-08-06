<?php

namespace Brouzie\Mailer\Model\Twig;

use Brouzie\Mailer\Model\Email;

class TwigContentEmail extends Email
{
    private $templatesContent;

    /**
     * @param string[] $templatesContent
     */
    public function __construct(array $templatesContent)
    {
        $this->templatesContent = $templatesContent;
    }

    public function getTemplateContent($block)
    {
        return isset($this->templatesContent[$block]) ? $this->templatesContent[$block] : null;
    }
}
