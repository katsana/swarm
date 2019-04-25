<?php

namespace Swarm;

use Illuminate\Support\Facades\Facade;

/**
 * @method \Symfony\Component\Routing\RouteCollection getRoutes()
 * @method void addRoute(string $method, string $uri, string|object $action)
 * @method void get(string $method, string|object $action)
 * @method void post(string $method, string|object $action)
 * @method void put(string $method, string|object $action)
 * @method void patch(string $method, string|object $action)
 * @method void delete(string $method, string|object $action)
 * @method void webSocket(string $method, string|object $action)
 *
 * @see \Swarm\Server\Router
 */
class Router extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'swarm.router';
    }
}
