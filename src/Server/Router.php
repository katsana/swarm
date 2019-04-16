<?php

namespace Swarm\Server;

use Illuminate\Contracts\Foundation\Application;
use Ratchet\WebSocket\MessageComponentInterface;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\LoopInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Router
{
    use Concerns\ProvidesRouting;

    /**
     * The app container implementation.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Construct router.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Symfony\Component\Routing\RouteCollection   $routes
     */
    public function __construct(Application $app, RouteCollection $routes)
    {
        $this->app = $app;
        $this->routes = $routes;
    }

    /**
     * Get routes collection.
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRoutes(): RouteCollection
    {
        return $this->routes;
    }

    /**
     * Create a websocket route.
     *
     * @param string        $uri
     * @param string|object $action
     *
     * @return void
     */
    public function webSocket(string $uri, $action): void
    {
        if (! \is_subclass_of($action, MessageComponentInterface::class)) {
            throw InvalidWebSocketController::withController($action);
        }

        $this->get($uri, $action);
    }

    /**
     * Get route.
     *
     * If the given action is a class that handles WebSockets, then it's not a regular
     * controller but a WebSocketHandler that needs to converted to a WsServer.
     *
     * If the given action is a regular controller we'll just instanciate it.
     *
     * @param string        $method
     * @param string        $uri
     * @param string|object $action
     *
     * @return \Symfony\Component\Routing\Route
     */
    protected function getRoute(string $method, string $uri, $action): Route
    {
        return new Route($uri, ['_controller' => $this->asController($action)], [], [], null, [], [$method]);
    }

    /**
     * Create websocket server or resolve handler.
     *
     * @param string $action
     *
     * @return \Ratchet\WebSocket\WsServer|object
     */
    protected function asController(string $action)
    {
        $handler = $this->app->make($action);

        if (\method_exists($handler, 'withEventLoop')) {
            $handler->withEventLoop($this->app[LoopInterface::class]);
        }

        if (\is_subclass_of($action, MessageComponentInterface::class)) {
            return new WsServer($handler);
        }

        return $handler;
    }
}
