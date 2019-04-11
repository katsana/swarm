<?php

namespace Swarm\Exceptions\Server;

use Exception;

class WebSocketException extends Exception
{
    /**
     * Get exception payload.
     *
     * @return array
     */
    public function getPayload(): array
    {
        return [
            'event' => 'error',
            'data' => [
                'message' => $this->getMessage(),
                'code' => $this->getCode(),
            ],
        ];
    }
