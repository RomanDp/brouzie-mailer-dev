<?php

namespace Brouzie\MailerBundle\DependencyInjection\Compiler;

use Brouzie\MailerBundle\Util\ContainerUtils;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AddRenderersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('brouzie_mailer.renderers.chain')) {
            return;
        }

        $renderers = ContainerUtils::findAndSortTaggedServices('brouzie_mailer.email_renderer', $container);

        if (1 === count($renderers)) {
            // Use an alias instead of wrapping it in the ChainRenderer for performances when using only one
            $container->setAlias('brouzie_mailer.renderer', new Alias((string)reset($renderers), false));
        } else {
            $definition = $container->getDefinition('brouzie_mailer.renderers.chain');
            //TODO: add support of service closure
            $definition->replaceArgument(0, $renderers);
            $container->setAlias('brouzie_mailer.renderer', new Alias('brouzie_mailer.renderers.chain', false));
        }
    }
}
