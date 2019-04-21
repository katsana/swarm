<?php

namespace Swarm\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ratchet\ConnectionInterface;
use React\Promise\PromiseInterface;
use Swarm\Socket\HttpComponent;

class ResponseFactory
{
    /**
     * Construct a response factory.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param \Illuminate|Http\Request     $request
     */
    public function __construct(ConnectionInterface $connection, Request $request)
    {
        $this->response = $response;
        $this->request = $request;
    }

    /**
     * Dispatch response to connection.
     *
     * @param mixed $response
     *
     * @return void
     */
    protected function dispatch($response): void
    {
        $connection->send(JsonResponse::create($response));
        $connection->close();
    }

    /**
     * Invoke the request and dispatch.
     *
     * @param \Swarm\Socket\HttpComponent $component
     *
     * @return void
     */
    public function __invoke(HttpComponent $component): void
    {
        $response = $component($this->request);

        if ($response instanceof PromiseInterface) {
            $response->then(function ($response) use ($dispatch) {
                $dispatch($response);
            });
        }

        $dispatch($response);
    }
}
