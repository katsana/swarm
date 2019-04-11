<?php

namespace Swarm\Exceptions\Server;

class InvalidConnection extends WebSocketException
{
    /**
     * Construct invalid connection exception.
     */
    public function __construct()
    {
        $this->message = 'Invalid Connection';

        $this->code = 4009;
    }
}
