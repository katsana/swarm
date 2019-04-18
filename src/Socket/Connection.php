<?php

namespace Swarm\Socket;

use Laravie\Stream\Logger;
use Ratchet\AbstractConnectionDecorator;
use Ratchet\ConnectionInterface;

class Connection extends AbstractConnectionDecorator implements ConnectionInterface
{
    /**
     * The logger implementation.
     *
     * @var \Laravie\Stream\Logger
     */
    protected $logger;

    /**
     * Construct connection logger.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param \Laravie\Stream\Logger       $logger
     */
    public function __construct(ConnectionInterface $connection, Logger $logger)
    {
        parent::__construct($connection);

        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function send($data)
    {
        $connection = $this->getConnection();

        $socketId = $connection->socketId ?? null;

        $this->logger->info("Connection ID {$socketId} sending message {$data}");

        $connection->send($data);
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $connection = $this->getConnection();

        $this->logger->warn("Connection ID {$connection->socketId} closing.");

        $connection->close();
    }
}
