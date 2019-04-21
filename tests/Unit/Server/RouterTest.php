<?php

namespace Swarm\Tests\Unit\Server;

use Illuminate\Contracts\Container\Container as ContainerContract;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;
use Ratchet\WebSocket\WsServer;
use Swarm\Server\Logger;
use Swarm\Server\Router;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouterTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_register_websocket_route()
    {
        $app = m::mock(ContainerContract::class);
        $routes = new RouteCollection();
        $logger = m::mock(Logger::class);

        $app->shouldReceive('make')->once()->with(StubMessageComponent::class)->andReturn($stub = new StubMessageComponent())
            ->shouldReceive('make')->once()->with('swarm.logger')->andReturn($logger);

        $router = new Router($app, $routes);

        $router->webSocket('stream', StubMessageComponent::class);
        $route = $routes->get('stream');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame(['GET'], $route->getMethods());
        $this->assertInstanceOf(WsServer::class, $route->getDefault('_controller'));
    }

    /** @test */
    public function it_cant_register_invalid_websocket_route()
    {
        $this->expectException('Swarm\Exceptions\InvalidWebSocketController');
        $this->expectExceptionMessage('Invalid WebSocket Controller provided. Expected instance of `Ratchet\WebSocket\MessageComponentInterface`, but received `StreamController`.');

        $app = m::mock(ContainerContract::class);
        $routes = new RouteCollection();

        $router = new Router($app, $routes);

        $router->webSocket('stream', 'StreamController');
    }
}

class StubMessageComponent implements MessageComponentInterface
{
    public function onMessage(ConnectionInterface $conn, MessageInterface $msg)
    {
        //
    }

    public function onOpen(ConnectionInterface $conn)
    {
        //
    }

    public function onClose(ConnectionInterface $conn)
    {
        //
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        //
    }
}
