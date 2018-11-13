<?php


namespace RandomQueue\Routing;


use RandomQueue\Controller\ControllerInterface;
use Symfony\Component\Routing\RouteCollection;

class RouteRegistry extends RouteCollection {

    /**
     * @param ControllerInterface $controller
     */
    public function addController(ControllerInterface $controller): void {
        $this->addCollection($controller->loadRoutes());
    }
}
