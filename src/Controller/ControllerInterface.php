<?php
namespace RandomQueue\Controller;

use Symfony\Component\Routing\RouteCollection;

interface ControllerInterface {

    /**
     * Returns route collection of this controller
     *
     * @return RouteCollection
     */
    public function loadRoutes(): RouteCollection;
}
