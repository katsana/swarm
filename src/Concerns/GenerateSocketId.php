<?php

namespace Swarm\Concerns;

use Ratchet\ConnectionInterface;

trait GenerateSocketId
{
    /**
     * Generate socket id for connection.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return $this
     */
    protected function generateSocketId(ConnectionInterface $connection)
    {
        $socketId = \sprintf('%d.%d', \random_int(1, 1000000000), \random_int(1, 1000000000));
        $connection->socketId = $socketId;

        return $this;
    }
}
