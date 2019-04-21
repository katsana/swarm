<?php

namespace Swarm\Tests\Unit\Server\Concerns;

use PHPUnit\Framework\TestCase;
use Swarm\Server\Concerns\ProvidesRouting;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ProvidesRoutingTest extends TestCase
{
    /** @test */
    public function it_can_register_get_route()
    {
        $routes = new RouteCollection();
        $router = new StubRouter($routes);

        $router->get('hello', 'HelloController');

        $route = $routes->get('hello');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame(['GET'], $route->getMethods());
        $this->assertSame('HelloController', $route->getDefault('_controller'));
    }

    /** @test */
    public function it_can_register_post_route()
    {
        $routes = new RouteCollection();
        $router = new StubRouter($routes);

        $router->post('hello', 'HelloController');

        $route = $routes->get('hello');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame(['POST'], $route->getMethods());
        $this->assertSame('HelloController', $route->getDefault('_controller'));
    }

    /** @test */
    public function it_can_register_put_route()
    {
        $routes = new RouteCollection();
        $router = new StubRouter($routes);

        $router->put('hello', 'HelloController');

        $route = $routes->get('hello');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame(['PUT'], $route->getMethods());
        $this->assertSame('HelloController', $route->getDefault('_controller'));
    }

    /** @test */
    public function it_can_register_patch_route()
    {
        $routes = new RouteCollection();
        $router = new StubRouter($routes);

        $router->patch('hello', 'HelloController');

        $route = $routes->get('hello');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame(['PATCH'], $route->getMethods());
        $this->assertSame('HelloController', $route->getDefault('_controller'));
    }

    /** @test */
    public function it_can_register_delete_route()
    {
        $routes = new RouteCollection();
        $router = new StubRouter($routes);

        $router->delete('hello', 'HelloController');

        $route = $routes->get('hello');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame(['DELETE'], $route->getMethods());
        $this->assertSame('HelloController', $route->getDefault('_controller'));
    }
}

class StubRouter
{
    use ProvidesRouting;

    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    protected function getRoute(string $method, string $uri, $action): Route
    {
        return new Route($uri, ['_controller' => $action], [], [], null, [], [$method]);
    }
}
