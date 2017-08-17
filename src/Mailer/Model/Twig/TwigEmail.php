<?php

namespace Brouzie\Mailer\Model\Twig;

use Brouzie\Mailer\Model\Address;
use Brouzie\Mailer\Model\Email;

class TwigEmail extends Email
{
    /**
     * @internal
     */
    const BLOCK_SUBJECT = 'subject';

    /**
     * @internal
     */
    const BLOCK_CONTENT = 'content';

    /**
     * @internal
     */
    const BLOCK_PLAIN_TEXT_CONTENT = 'plain_text_content';

    /**
     * @internal
     */
    const BLOCK_HEADERS = 'headers';

    private $template;

    public function __construct(string $template)
    {
        $this->template = $template;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     * @param Address|Address[] $recipients
     * @param Address|null $sender
     *
     * @return self
     */
    public static function create(string $template, $recipients, Address $sender = null)
    {
        $email = new static($template);

        $recipients = is_array($recipients) ? $recipients : [$recipients];
        foreach ($recipients as $recipient) {
            $email->addRecipient($recipient);
        }

        if (null !== $sender) {
            $email->setSender($sender);
        }

        return $email;
    }
}
