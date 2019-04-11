<?php

namespace Swarm\Server\Concerns;

use Symfony\Component\Routing\Route;

trait ProvidesRouting
{
    /**
     * The Route collection.
     *
     * @var \Symfony\Component\Routing\RouteCollection
     */
    protected $routes;

    /**
     * Add GET route.
     *
     * @param string        $uri
     * @param string|object $action
     *
     * @return void
     */
    public function get(string $uri, $action): void
    {
        $this->addRoute('GET', $uri, $action);
    }

    /**
     * Add POST route.
     *
     * @param string        $uri
     * @param string|object $action
     *
     * @return void
     */
    public function post(string $uri, $action): void
    {
        $this->addRoute('POST', $uri, $action);
    }

    /**
     * Add PUT route.
     *
     * @param string        $uri
     * @param string|object $action
     *
     * @return void
     */
    public function put(string $uri, $action): void
    {
        $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Add PATCH route.
     *
     * @param string        $uri
     * @param string|object $action
     *
     * @return void
     */
    public function patch(string $uri, $action): void
    {
        $this->addRoute('PATCH', $uri, $action);
    }

    /**
     * Add DELETE route.
     *
     * @param string        $uri
     * @param string|object $action
     *
     * @return void
     */
    public function delete(string $uri, $action): void
    {
        $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Add route to route collection.
     *
     * @param string        $method
     * @param string        $uri
     * @param string|object $action
     *
     * @return void
     */
    public function addRoute(string $method, string $uri, $action): void
    {
        $this->routes->add($uri, $this->getRoute($method, $uri, $action));
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
    abstract protected function getRoute(string $method, string $uri, $action): Route;
}
