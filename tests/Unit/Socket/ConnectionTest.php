<?php

namespace Swarm\Tests\Unit\Socket;

use Mockery as m;
use Swarm\Socket\Connection;
use PHPUnit\Framework\TestCase;
use Ratchet\ConnectionInterface;
use Swarm\Server\Logger;

class ConnectionTest extends TestCase
{
    /**
     * Teardoen the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_log_on_sending_message_to_connection()
    {
        $connection = m::mock(ConnectionInterface::class);
        $logger = m::mock(Logger::class);

        $connection->shouldReceive('send')->once()->with('foobar');
        $logger->shouldReceive('onMessageBroadcast')->once()->with($connection, 'foobar');

        $socket = new Connection($connection, $logger);

        $socket->send('foobar');
        $this->addToAssertionCount(2);
    }

    /** @test */
    public function it_can_log_on_closing_connection()
    {
        $connection = m::mock(ConnectionInterface::class);
        $logger = m::mock(Logger::class);

        $connection->shouldReceive('close')->once()->with();
        $logger->shouldReceive('onConnectionUpdate')->once()->with($connection, 'closing connection');

        $socket = new Connection($connection, $logger);

        $socket->close();

        $this->addToAssertionCount(2);
    }
}
