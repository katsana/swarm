<?php

namespace Swarm;

use Illuminate\Support\Facades\Facade;

/**
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