<?php

namespace Swarm\Socket;

use Laravie\Stream\Logger;
use Ratchet\ConnectionInterface;
use Ratchet\WsConnection;

class Connection extends WsConnection implements ConnectionInterface
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
        $socketId = $this->getConnection()->socketId ?? null;

        $this->logger->info("Connection ID {$socketId} sending message {$data}");

        parent::send($data);
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->logger->warn("Connection ID {$this->getConnection()->socketId} closing.");

        parent::close();
    }
}
