<?php

namespace Swarm\Server;

use Ratchet\WebSocket\MessageComponentInterface;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Router
{
    use Concerns\ProvidesRouting;

    /**
     * Construct router.
     *
     * @param \Symfony\Component\Routing\RouteCollection $routes
     */
    public function __construct(RouteCollection $routes)
    {
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
        $action = \is_subclass_of($action, MessageComponentInterface::class)
                        ? $this->createWebSocketServer($action)
                        : \app($action);

        return new Route($uri, ['_controller' => $action], [], [], null, [], [$method]);
    }

    /**
     * Create websocket server.
     *
     * @param string $action
     *
     * @return \Ratchet\WebSocket\WsServer
     */
    protected function createWebSocketServer(string $action): WsServer
    {
        return new WsServer(\app($action));
    }
}
