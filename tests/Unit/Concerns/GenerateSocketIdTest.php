<?php

namespace Swarm\Tests\Unit\Concerns;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Ratchet\ConnectionInterface;
use Ratchet\WebSocket\WsConnection;
use Swarm\Concerns\GenerateSocketId;

class GenerateSocketIdTest extends TestCase
{
    use GenerateSocketId;

    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_generate_socket_id()
    {
        $connection = m::mock(ConnectionInterface::class);
        $wsConnection = new WsConnection($connection);

        $this->assertFalse(isset($wsConnection->socketId));

        $this->generateSocketId($wsConnection);

        $this->assertRegExp('/\d+\.\d+/', $wsConnection->socketId);
        $this->assertTrue(isset($wsConnection->socketId));
    }
}
