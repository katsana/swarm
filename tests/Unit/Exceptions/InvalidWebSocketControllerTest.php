<?php

namespace Swarm\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Swarm\Exceptions\InvalidWebSocketController;

class InvalidWebSocketControllerTest extends TestCase
{
    /** @test */
    public function it_has_proper_signature()
    {
        $this->expectException(InvalidWebSocketController::class);
        $this->expectExceptionMessage("Invalid WebSocket Controller provided. Expected instance of `Ratchet\WebSocket\MessageComponentInterface`, but received `foo`.");

        throw InvalidWebSocketController::withController('foo');
    }
}
