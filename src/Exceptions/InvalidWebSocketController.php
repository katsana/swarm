<?php

namespace Swarm\Exceptions;

use Exception;

class InvalidWebSocketController extends Exception
{
    /**
     * Trigger invalid controller exception.
     *
     * @param string $controller
     *
     * @return static
     */
    public static function withController(string $controller)
    {
        return new static(
            "Invalid WebSocket Controller provided. Expected instance of `Ratchet\WebSocket\MessageComponentInterface`, but received `{$controller}`."
        );
    }
}
