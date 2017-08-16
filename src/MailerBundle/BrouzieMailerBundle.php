<?php

namespace Brouzie\MailerBundle;

use Brouzie\MailerBundle\DependencyInjection\Compiler\AddRenderersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BrouzieMailerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddRenderersPass());
    }
}
