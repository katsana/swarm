<?php

namespace Swarm\Tests\Unit\Server;

use Laravie\Stream\Log\Console;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\WsConnection;
use Swarm\Server\Logger;
use Symfony\Component\Console\Output\OutputInterface;

class LoggerTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_has_proper_signature()
    {
        $console = m::mock(Console::class);

        $logger = new Logger($console);

        $this->assertSame(OutputInterface::VERBOSITY_NORMAL, $logger->getVerbosity());
    }

    /** @test */
    public function it_can_set_verbosity()
    {
        $console = m::mock(Console::class);

        $logger = new Logger($console);

        $logger->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);

        $this->assertSame(OutputInterface::VERBOSITY_VERY_VERBOSE, $logger->getVerbosity());
    }

    /** @test */
    public function it_can_set_verbosity_from_console_output()
    {
        $console = m::mock(Console::class);
        $output = m::mock(OutputInterface::class);

        $output->shouldReceive('getVerbosity')->once()->andReturn(OutputInterface::VERBOSITY_VERY_VERBOSE);

        $logger = new Logger($console);

        $logger->fromConsoleOutput($output);

        $this->assertSame(OutputInterface::VERBOSITY_VERY_VERBOSE, $logger->getVerbosity());
    }

    /** @test */
    public function it_can_log_connection_update()
    {
        $connection = new WsConnection(m::mock(ConnectionInterface::class));
        $console = m::mock(Console::class);

        $connection->socketId = 'foobar';

        $console->shouldReceive('warn')->once()->with('[Conn:foobar] connection opened.');

        $logger = new Logger($console);

        $logger->onConnectionUpdate($connection, 'connection opened');

        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_can_log_message_broadcast()
    {
        $connection = new WsConnection(m::mock(ConnectionInterface::class));
        $console = m::mock(Console::class);

        $connection->socketId = 'foobar';

        $console->shouldReceive('info')->once()->with('[Conn:foobar] broadcast message: Hello world.');

        $logger = new Logger($console);
        $logger->setVerbosity(OutputInterface::VERBOSITY_DEBUG);

        $logger->onMessageBroadcast($connection, 'Hello world');

        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_cant_log_message_broadcast_without_proper_verbosity()
    {
        $connection = new WsConnection(m::mock(ConnectionInterface::class));
        $console = m::mock(Console::class);

        $connection->socketId = 'foobar';

        $console->shouldReceive('info')->never()->with('[Conn:foobar] broadcast message: Hello world.');

        $logger = new Logger($console);

        $logger->onMessageBroadcast($connection, 'Hello world');

        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_can_log_message_received()
    {
        $connection = new WsConnection(m::mock(ConnectionInterface::class));
        $console = m::mock(Console::class);
        $message = m::mock(MessageInterface::class);

        $connection->socketId = 'foobar';

        $console->shouldReceive('info')->once()->with('[Conn:foobar] received message: Hello foobar.');
        $message->shouldReceive('getPayload')->andReturn('Hello foobar');

        $logger = new Logger($console);
        $logger->setVerbosity(OutputInterface::VERBOSITY_DEBUG);

        $logger->onMessageReceived($connection, $message);

        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_cant_log_message_received_without_proper_verbosity()
    {
        $connection = new WsConnection(m::mock(ConnectionInterface::class));
        $console = m::mock(Console::class);
        $message = m::mock(MessageInterface::class);

        $connection->socketId = 'foobar';

        $console->shouldReceive('info')->never()->with('[Conn:foobar] received message: Hello foobar.');
        $message->shouldReceive('getPayload')->never()->andReturn('Hello foobar');

        $logger = new Logger($console);

        $logger->onMessageReceived($connection, $message);

        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_can_log_error()
    {
        $console = m::mock(Console::class);

        $console->shouldReceive('error')->once()->with('Exception `Exception` thrown: `Data is invalid`.');

        $logger = new Logger($console);

        $logger->onError(new \Exception('Data is invalid'));

        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_can_forward_call_to_logger()
    {
        $console = m::mock(Console::class);

        $console->shouldReceive('info')->once()->with('Hello world');

        $logger = new Logger($console);

        $logger->info('Hello world');

        $this->addToAssertionCount(1);
    }
}
