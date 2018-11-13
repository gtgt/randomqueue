<?php
namespace RandomQueue\Controller;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

abstract class AbstractController implements ControllerInterface {

    protected const CONTROLLER_ACTIONS = [];

    /**
     * {@inheritDoc}
     */
    public function LoadRoutes(): RouteCollection {
        $routes = new RouteCollection();
        foreach (static::CONTROLLER_ACTIONS as $path => $method) {
            $routes->add(static::getRouteName($path), new Route($path, ['_controller' => [$this, $method.'Action']]));
        }
        return $routes;
    }

    /**
     * Makes route name from path
     *
     * @param $path
     *
     * @return string
     */
    protected static function getRouteName($path) {
        return trim(str_replace('/', '_', $path), '_');
    }
}
