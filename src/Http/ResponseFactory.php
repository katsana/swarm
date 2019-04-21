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
     * The connection implementation.
     *
     * @var \Ratchet\ConnectionInterface
     */
    protected $connection;

    /**
     * The request.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Construct a response factory.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param \Illuminate\Http\Request     $request
     */
    public function __construct(ConnectionInterface $connection, Request $request)
    {
        $this->connection = $connection;
        $this->request = $request;
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
            $response->then(function ($message) {
                $this->dispatch($message);
            });

            return;
        }

        $this->dispatch($response);
    }

    /**
     * Dispatch response to connection.
     *
     * @param mixed $response
     *
     * @return void
     */
    public function dispatch($response): void
    {
        $this->connection->send(JsonResponse::create($response));
        $this->connection->close();
    }
}
