<?php

namespace Swarm\Socket;

use Exception;
use Laravie\Stream\Logger;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;

class MessageComponent implements MessageComponentInterface
{
    /**
     * Component implementation.
     *
     * @var \Ratchet\Http\HttpServerInterface
     */
    protected $component;

    /**
     * The logger implementation.
     *
     * @var \Laravie\Stream\Logger
     */
    protected $logger;

    /**
     * Construct a new message component decorator.
     *
     * @param \Ratchet\WebSocket\MessageComponentInterface $component
     * @param \Laravie\Stream\Logger                       $logger
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
        $socketId = $connection->socketId ?? null;

        $this->logger->warn("New connection opened for {$socketId}.");

        $this->component->onOpen(new Connection($connection, $this->logger));
    }

    /**
     * {@inheritdoc}
     */
    public function onMessage(ConnectionInterface $connection, MessageInterface $message)
    {
        $socketId = $connection->socketId ?? null;

        $this->logger->info("Connection ID {$socketId} received message: {$message->getPayload()}.");

        $this->component->onMessage(new Connection($connection, $this->logger), $message);
    }

    /**
     * {@inheritdoc}
     */
    public function onClose(ConnectionInterface $connection)
    {
        $socketId = $connection->socketId ?? null;

        $this->logger->warn("Connection ID {$socketId} closed.");

        $this->component->onClose(new Connection($connection, $this->logger));
    }

    /**
     * {@inheritdoc}
     */
    public function onError(ConnectionInterface $connection, Exception $exception)
    {
        $exceptionClass = \get_class($exception);

        $this->logger->error("Exception `{$exceptionClass}` thrown: `{$exception->getMessage()}`.");

        $this->component->onError(new Connection($connection, $this->logger), $exception);
    }
}
