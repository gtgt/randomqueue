<?php
namespace RandomQueue\DependencyInjection\Compiler;

use RandomQueue\Controller\ControllerInterface;
use RandomQueue\Routing\RouteRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Class ControllerServicePass
 *
 * Creates a RouteCollection service to include all routes defined by ControllerInterface implemented classes.
 */
class ControllerServicePass implements CompilerPassInterface {

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container) {
        $routeRegistryDef = $container->getDefinition(RouteRegistry::class);

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
            if (is_subclass_of($className, ControllerInterface::class)) {
                $definition->setPublic(TRUE);
                $routeRegistryDef->addMethodCall('addController', [$definition]);
            }
        }
    }
}
