<?php

namespace Swarm\Socket;

use Exception;
use Illuminate\Database\DetectsLostConnections;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;
use Swarm\Concerns\GenerateSocketId;
use Swarm\Server\Logger;

class MessageComponent implements MessageComponentInterface
{
    use DetectsLostConnections, GenerateSocketId;

    /**
     * Component implementation.
     *
     * @var \Ratchet\Http\HttpServerInterface
     */
    protected $component;

    /**
     * The logger implementation.
     *
     * @var \Swarm\Server\Logger
     */
    protected $logger;

    /**
     * Construct a new message component decorator.
     *
     * @param \Ratchet\WebSocket\MessageComponentInterface $component
     * @param \Swarm\Server\Logger                         $logger
     */
    public function __construct(MessageComponentInterface $component, Logger $logger)
    {
        $this->component = $component;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function onOpen(ConnectionInterface $connection)
    {
        if (! isset($connection->socketId)) {
            $this->generateSocketId($connection);
        }

        $this->logger->onConnectionUpdate($connection, 'connection opened');

        $this->component->onOpen(new Connection($connection, $this->logger));
    }

    /**
     * {@inheritdoc}
     */
    public function onMessage(ConnectionInterface $connection, MessageInterface $message)
    {
        $this->logger->onMessageReceived($connection, $message);

        $this->component->onMessage(new Connection($connection, $this->logger), $message);
    }

    /**
     * {@inheritdoc}
     */
    public function onClose(ConnectionInterface $connection)
    {
        $this->logger->onConnectionUpdate($connection, 'connection closed');

        $this->component->onClose(new Connection($connection, $this->logger));
    }

    /**
     * {@inheritdoc}
     */
    public function onError(ConnectionInterface $connection, Exception $exception)
    {
        $this->logger->onError($exception);

        $this->component->onError(new Connection($connection, $this->logger), $exception);

        if ($this->causedByLostConnection($exception)) {
            exit(0);
        }
    }
}
