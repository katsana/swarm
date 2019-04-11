<?php

namespace Swarm\Exceptions\Server;

class InvalidChannel extends WebSocketException
{
    /**
     * Construct invalid channel exception.
     */
    public function __construct(string $channel)
    {
        $this->message = "Could not find channel `{$channel}`.";

        $this->code = 4001;
    }
}
