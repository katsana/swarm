<?php

namespace Swarm\Tests\Feature\Server;

use Swarm\Tests\TestCase;

class Router extends TestCase
{
    /** @test */
    public function it_has_proper_signature()
    {
        $router = $this->app['swarm.router'];

        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $router->getRoutes());
    }
}
