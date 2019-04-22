<?php

namespace Swarm\Tests\Unit\Server;

use Illuminate\Contracts\Container\Container as ContainerContract;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\LoopInterface;
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
    public function it_can_register_http_route()
    {
        $app = m::mock(ContainerContract::class);
        $routes = new RouteCollection();

        $app->shouldReceive('make')->once()->with(HttpMessageComponent::class)->andReturn($stub = new HttpMessageComponent())
            ->shouldReceive('make')->never()->with('swarm.logger');

        $router = new Router($app, $routes);

        $router->get('hello', HttpMessageComponent::class);
        $route = $routes->get('hello');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame(['GET'], $route->getMethods());
        $this->assertInstanceOf(HttpMessageComponent::class, $route->getDefault('_controller'));
    }

    /** @test */
    public function it_can_register_http_route_with_event_loop()
    {
        $app = m::mock(ContainerContract::class);
        $routes = new RouteCollection();
        $eventLoop = m::mock(LoopInterface::class);

        $app->shouldReceive('make')->once()->with(HttpMessageComponentWithEventLoop::class)->andReturn($stub = new HttpMessageComponentWithEventLoop())
            ->shouldReceive('make')->never()->with('swarm.logger')
            ->shouldReceive('bound')->once()->with(LoopInterface::class)->andReturn(true)
            ->shouldReceive('make')->once()->with(LoopInterface::class)->andReturn($eventLoop);

        $router = new Router($app, $routes);

        $router->get('hello', HttpMessageComponentWithEventLoop::class);
        $route = $routes->get('hello');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame(['GET'], $route->getMethods());
        $this->assertInstanceOf(HttpMessageComponent::class, $route->getDefault('_controller'));
    }

    /** @test */
    public function it_can_register_websocket_route()
    {
        $app = m::mock(ContainerContract::class);
        $routes = new RouteCollection();
        $logger = m::mock(Logger::class);

        $app->shouldReceive('make')->once()->with(WebSocketMessageComponent::class)->andReturn($stub = new WebSocketMessageComponent())
            ->shouldReceive('make')->once()->with('swarm.logger')->andReturn($logger);

        $router = new Router($app, $routes);

        $router->webSocket('stream', WebSocketMessageComponent::class);
        $route = $routes->get('stream');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame(['GET'], $route->getMethods());
        $this->assertInstanceOf(WsServer::class, $route->getDefault('_controller'));
    }

    /** @test */
    public function it_can_register_websocket_route_with_event_loop()
    {
        $app = m::mock(ContainerContract::class);
        $routes = new RouteCollection();
        $logger = m::mock(Logger::class);
        $eventLoop = m::mock(LoopInterface::class);

        $app->shouldReceive('make')->once()->with(WebSocketMessageComponentWithEventLoop::class)->andReturn($stub = new WebSocketMessageComponentWithEventLoop())
            ->shouldReceive('make')->once()->with('swarm.logger')->andReturn($logger)
            ->shouldReceive('bound')->once()->with(LoopInterface::class)->andReturn(true)
            ->shouldReceive('make')->once()->with(LoopInterface::class)->andReturn($eventLoop);

        $router = new Router($app, $routes);

        $router->webSocket('stream', WebSocketMessageComponentWithEventLoop::class);
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

class HttpMessageComponent
{
}

class HttpMessageComponentWithEventLoop extends HttpMessageComponent
{
    public function withEventLoop(LoopInterface $loop)
    {
        //
    }
}

class WebSocketMessageComponent implements MessageComponentInterface
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

class WebSocketMessageComponentWithEventLoop extends WebSocketMessageComponent
{
    public function withEventLoop(LoopInterface $loop)
    {
        //
    }
}
