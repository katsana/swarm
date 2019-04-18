<?php

namespace Swarm\Server;

use Illuminate\Contracts\Foundation\Application;
use Laravie\Stream\Logger;
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
        $app = $this->app;
        $handler = $app->make($action);

        if (\method_exists($handler, 'withEventLoop') && $this->app->bound(LoopInterface::class)) {
            $handler->withEventLoop($app->make(LoopInterface::class));
        }

        if (\is_subclass_of($action, MessageComponentInterface::class)) {
            $component = $app->bound(Logger::class)
                            ? new MessageComponent($handler, $this->app->make(Logger::class))
                            : $handler;

            return new WsServer($component);
        }

        return $handler;
    }
}
