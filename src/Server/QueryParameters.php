<?php

namespace Swarm\Server;

use Psr\Http\Message\RequestInterface;

class QueryParameters
{
    /**
     * The request implementation.
     *
     * @var \Psr\Http\Message\RequestInterface
     */
    protected $request;

    /**
     * Create from request interface.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return static
     */
    public static function create(RequestInterface $request)
    {
        return new static($request);
    }

    /**
     * Construct a new query parameters.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Get all query parameters.
     *
     * @return array
     */
    public function all(): array
    {
        $parameters = [];

        \parse_str($this->request->getUri()->getQuery(), $parameters);

        return $parameters;
    }

    /**
     * Get query parameter by name.
     *
     * @param string $name
     *
     * @return string|null
     */
    public function get(string $name): ?string
    {
        return $this->all()[$name] ?? null;
    }
}
