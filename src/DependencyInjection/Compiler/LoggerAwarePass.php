<?php
namespace RandomQueue\DependencyInjection\Compiler;

use Psr\Log\LoggerAwareInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Class LoggerAwarePass
 *
 * Helper to set logger for the classes using LoggerAwareInterface
 */
class LoggerAwarePass implements CompilerPassInterface {

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container) {
        $logger = new Reference('logger');

        foreach ($container->getServiceIds() as $id) {
            if ($container->hasAlias($id)) {
                // don't brother with alias. the aliased service will be processed
                continue;
            }
            $definition = $container->getDefinition($id);
            // syntetic services aren't count too
            if ($definition->isSynthetic()) {
                continue;
            }

            // resolve classname
            $className = $container->getParameterBag()->resolveValue($definition->getClass());
            if (is_subclass_of($className, LoggerAwareInterface::class)) {
                $definition->addMethodCall('setLogger', [$logger]);
            }
        }
    }
}
