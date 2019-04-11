<?php

namespace Swarm\Exceptions\Server;

class InvalidSignature extends WebSocketException
{
    /**
     * Construct invalid signature exception.
     */
    public function __construct()
    {
        $this->message = 'Invalid Signature';

        $this->code = 4009;
    }
}
