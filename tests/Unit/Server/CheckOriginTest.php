<?php

namespace Swarm\Tests\Unit\Server;

use Mockery as m;
use Swarm\Server\CheckOrigin;
use PHPUnit\Framework\TestCase;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Psr\Http\Message\RequestInterface;

class CheckOriginTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_detect_valid_origin()
    {
        $component = m::mock(MessageComponentInterface::class);
        $origins = ['127.0.0.1'];
        $connection = m::mock(ConnectionInterface::class);
        $request = m::mock(RequestInterface::class);

        $request->shouldReceive('hasHeader')->once()->with('Origin')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('Origin')->andReturn(['127.0.0.1:8080']);

        $component->shouldReceive('onOpen')->once()->with($connection, $request);

        $checker = new CheckOrigin($component, $origins);
        $checker->onOpen($connection, $request);

        $this->addToAssertionCount(2);
    }

    /** @test */
    public function it_can_detect_invalid_origin()
    {
        $component = m::mock(MessageComponentInterface::class);
        $origins = ['0.0.0.0'];
        $connection = m::mock(ConnectionInterface::class);
        $request = m::mock(RequestInterface::class);

        $request->shouldReceive('hasHeader')->once()->with('Origin')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('Origin')->andReturn(['127.0.0.1:8080']);

        $connection->shouldReceive('send')->once()->with(m::type('String'))
            ->shouldReceive('close')->once();

        $checker = new CheckOrigin($component, $origins);
        $checker->onOpen($connection, $request);

        $this->addToAssertionCount(2);
    }

    /** @test */
    public function it_can_ignore_origin_if_header_isnt_set()
    {
        $component = m::mock(MessageComponentInterface::class);
        $origins = ['0.0.0.0'];
        $connection = m::mock(ConnectionInterface::class);
        $request = m::mock(RequestInterface::class);

        $request->shouldReceive('hasHeader')->once()->with('Origin')->andReturn(false);

        $component->shouldReceive('onOpen')->once()->with($connection, $request);

        $checker = new CheckOrigin($component, $origins);
        $checker->onOpen($connection, $request);

        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_pass_through_message_request()
    {
        $component = m::mock(MessageComponentInterface::class);
        $origins = [];
        $connection = m::mock(ConnectionInterface::class);

        $component->shouldReceive('onMessage')->once()->with($connection, 'Hello world');

        $checker = new CheckOrigin($component, $origins);
        $checker->onMessage($connection, 'Hello world');

        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_pass_through_close_request()
    {
        $component = m::mock(MessageComponentInterface::class);
        $origins = [];
        $connection = m::mock(ConnectionInterface::class);

        $component->shouldReceive('onClose')->once()->with($connection);

        $checker = new CheckOrigin($component, $origins);
        $checker->onClose($connection);

        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_pass_through_request_with_error()
    {
        $component = m::mock(MessageComponentInterface::class);
        $origins = [];
        $connection = m::mock(ConnectionInterface::class);
        $exception = new \Exception('Data is invalid');

        $component->shouldReceive('onError')->once()->with($connection, $exception);

        $checker = new CheckOrigin($component, $origins);
        $checker->onError($connection, $exception);

        $this->addToAssertionCount(1);
    }
}
