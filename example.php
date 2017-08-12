<?php

use Brouzie\Mailer\Mailer;
use Brouzie\Mailer\Model\Address;
use Brouzie\Mailer\Model\Twig\TwigEmail;
use Brouzie\Mailer\Renderer\TwigRenderer;
use Brouzie\Mailer\SwiftMailerTransport;

// https://github.com/Sylius/SyliusMailerBundle
// http://docs.sylius.org/en/latest/bundles/SyliusMailerBundle/configuration.html
// https://github.com/Sylius/Mailer/
// https://github.com/Sylius/Sylius/issues/6749

$mailer = new Mailer(
    new TwigRenderer(new Twig\Environment()),
    ['default' => new SwiftMailerTransport(new Swift_Mailer(new Swift_SmtpTransport()))],
    'default',
    []
);

$email = TwigEmail::create('@App/emails/user_registration.html.twig', new Address('koc-dp@yandex.ru'));
$email->setSender(new Address(''));

$mailer->sendEmail($email);
