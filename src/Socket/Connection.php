<?php

namespace Swarm\Socket;

use Ratchet\AbstractConnectionDecorator;
use Ratchet\ConnectionInterface;
use Swarm\Server\Logger;

class Connection extends AbstractConnectionDecorator implements ConnectionInterface
{
    /**
     * The logger implementation.
     *
     * @var \Swarm\Server\Logger
     */
    protected $logger;

    /**
     * Construct connection logger.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param \Swarm\Server\Logger         $logger
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

        $this->logger->onMessageBroadcast($connection, $data);

        $connection->send($data);
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $connection = $this->getConnection();

        $this->logger->onConnectionUpdate($connection, 'closing connection');

        $connection->close();
    }
}
