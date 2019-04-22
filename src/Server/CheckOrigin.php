<?php

namespace Swarm\Server;

use Exception;
use Psr\Http\Message\RequestInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\CloseResponseTrait;
use Ratchet\Http\HttpServerInterface;
use Ratchet\MessageComponentInterface;

class CheckOrigin implements HttpServerInterface
{
    use CloseResponseTrait;

    /**
     * The message component.
     *
     * @var \Ratchet\MessageComponentInterface
     */
    protected $component;

    /**
     * List of allowed origins.
     *
     * @var array
     */
    protected $allowedOrigins = [];

    /**
     * Construct origin checker.
     *
     * @param \Ratchet\MessageComponentInterface $component
     * @param array                              $allowedOrigins
     */
    public function __construct(MessageComponentInterface $component, array $allowedOrigins = [])
    {
        $this->component = $component;

        $this->allowedOrigins = $allowedOrigins;
    }

    /**
     * {@inheritdoc}
     */
    public function onOpen(ConnectionInterface $connection, RequestInterface $request = null)
    {
        if (! $this->verifyOrigin($connection, $request)) {
            $this->close($connection, 403);
        } else {
            $this->component->onOpen($connection, $request);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onMessage(ConnectionInterface $from, $message)
    {
        $this->component->onMessage($from, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function onClose(ConnectionInterface $connection)
    {
        $this->component->onClose($connection);
    }

    /**
     * {@inheritdoc}
     */
    public function onError(ConnectionInterface $connection, Exception $exception)
    {
        $this->component->onError($connection, $exception);
    }

    /**
     * Verify connection origin.
     *
     * @param \Ratchet\ConnectionInterface       $connection
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return bool
     */
    protected function verifyOrigin(ConnectionInterface $connection, RequestInterface $request): bool
    {
        // Skip check if Origin header is not present.
        if (! $request->hasHeader('Origin')) {
            return true;
        }

        $header = (string) $request->getHeader('Origin')[0];
        $origin = \parse_url($header, PHP_URL_HOST) ?: $header;

        if (! empty($this->allowedOrigins) && ! \in_array($origin, $this->allowedOrigins)) {
            return false;
        }

        return true;
    }
}
