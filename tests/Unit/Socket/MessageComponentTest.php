<?php

namespace Swarm\Tests\Unit\Socket;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;
use Ratchet\WebSocket\WsConnection;
use Swarm\Server\Logger;
use Swarm\Socket\MessageComponent;

class MessageComponentTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_log_on_open_connection()
    {
        $message = m::mock(MessageComponentInterface::class);
        $logger = m::mock(Logger::class);
        $connection = new WsConnection(m::mock(ConnectionInterface::class));

        $logger->shouldReceive('onConnectionUpdate')->once()->with($connection, 'connection opened');
        $message->shouldReceive('onOpen')->once()->andReturnUsing(function ($conn) {
            $this->assertInstanceOf('Swarm\Socket\Connection', $conn);
        });

        $component = new MessageComponent($message, $logger);

        $component->onOpen($connection);

        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_can_log_on_connection_receive_message()
    {
        $message = m::mock(MessageComponentInterface::class);
        $logger = m::mock(Logger::class);
        $connection = new WsConnection(m::mock(ConnectionInterface::class));
        $payload = m::mock(MessageInterface::class);

        $logger->shouldReceive('onMessageReceived')->once()->with($connection, $payload);
        $message->shouldReceive('onMessage')->once()->andReturnUsing(function ($conn, $msg) use ($payload) {
            $this->assertInstanceOf('Swarm\Socket\Connection', $conn);
            $this->assertSame($payload, $msg);
        });

        $component = new MessageComponent($message, $logger);

        $component->onMessage($connection, $payload);

        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_can_log_on_close_connection()
    {
        $message = m::mock(MessageComponentInterface::class);
        $logger = m::mock(Logger::class);
        $connection = new WsConnection(m::mock(ConnectionInterface::class));

        $logger->shouldReceive('onConnectionUpdate')->once()->with($connection, 'connection closed');
        $message->shouldReceive('onClose')->once()->andReturnUsing(function ($conn) {
            $this->assertInstanceOf('Swarm\Socket\Connection', $conn);
        });

        $component = new MessageComponent($message, $logger);

        $component->onClose($connection);

        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_can_log_on_error()
    {
        $message = m::mock(MessageComponentInterface::class);
        $logger = m::mock(Logger::class);
        $connection = new WsConnection(m::mock(ConnectionInterface::class));
        $exception = new \Exception('Data is invalid');

        $logger->shouldReceive('onError')->once()->with($exception);
        $message->shouldReceive('onError')->once()->andReturnUsing(function ($conn, $e) use ($exception) {
            $this->assertInstanceOf('Swarm\Socket\Connection', $conn);
            $this->assertSame($exception, $e);
        });

        $component = new MessageComponent($message, $logger);

        $component->onError($connection, $exception);

        $this->addToAssertionCount(1);
    }
}
