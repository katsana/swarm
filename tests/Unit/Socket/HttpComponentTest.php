<?php

namespace Swarm\Tests\Unit\Socket;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Swarm\Socket\HttpComponent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpComponentTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_handle_request_on_open_connection()
    {
        $connection = m::mock(ConnectionInterface::class);
        $request = m::mock(RequestInterface::class);
        $uri = m::mock(UriInterface::class);

        $connection->shouldReceive('send')->once()->with(m::type(JsonResponse::class))
            ->shouldReceive('close')->once();

        $uri->shouldReceive('getHost')->times(2)->andReturn('127.0.0.1')
            ->shouldReceive('getPort')->times(2)->andReturn(8085)
            ->shouldReceive('getPath')->times(1)->andReturn('hello')
            ->shouldReceive('getQuery')->times(2)->andReturn('foo=bar');

        $request->shouldReceive('getHeaders')->twice()->andReturn([
            'Content-Length' => [11],
        ])
        ->shouldReceive('getMethod')->times(1)->andReturn('GET')
        ->shouldReceive('getUri')->times(2)->andReturn($uri)
        ->shouldReceive('getProtocolVersion')->times(1)->andReturn('1.1')
        ->shouldReceive('getBody')->times(1)->andReturn('Hello world');

        $http = new class() extends HttpComponent {
            public function __invoke(Request $request)
            {
                return ['message' => 'Hello world'];
            }
        };

        $http->onOpen($connection, $request);

        $this->addToAssertionCount(2);
    }

    /** @test */
    public function it_can_handle_request_on_message_connection()
    {
        $connection = m::mock(ConnectionInterface::class);
        $request = m::mock(RequestInterface::class);
        $uri = m::mock(UriInterface::class);
        $message = m::mock(MessageInterface::class);

        $connection->shouldReceive('send')->once()->with(m::type(JsonResponse::class))
            ->shouldReceive('close')->once();

        $uri->shouldReceive('getHost')->times(2)->andReturn('127.0.0.1')
            ->shouldReceive('getPort')->times(2)->andReturn(8085)
            ->shouldReceive('getPath')->times(1)->andReturn('hello')
            ->shouldReceive('getQuery')->times(2)->andReturn('foo=bar');

        $request->shouldReceive('getHeaders')->times(2)->andReturn([
            'Content-Length' => [11],
        ])
        ->shouldReceive('getMethod')->times(1)->andReturn('GET')
        ->shouldReceive('getUri')->times(2)->andReturn($uri)
        ->shouldReceive('getProtocolVersion')->times(1)->andReturn('1.1')
        ->shouldReceive('getBody')->times(1)->andReturn('Hello ');

        $message->shouldReceive('getContents')->once()->andReturn('world');

        $http = new class() extends HttpComponent {
            public function __invoke(Request $request)
            {
                return ['message' => 'Hello world'];
            }
        };

        $http->onOpen($connection, $request);
        $http->onMessage($connection, $message);

        $this->addToAssertionCount(2);
    }

    /** @test */
    public function it_does_nothing_on_close_connection()
    {
        $connection = m::mock(ConnectionInterface::class);

        $connection->shouldReceive('send')->never()
            ->shouldReceive('close')->never();

        $http = new class() extends HttpComponent {
            public function __invoke(Request $request)
            {
                return ['message' => 'Hello world'];
            }
        };

        $http->onClose($connection);

        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_can_handle_request_on_none_http_error()
    {
        $connection = m::mock(ConnectionInterface::class);
        $exception = new \Exception('Data is invalid');

        $connection->shouldReceive('send')->never()
            ->shouldReceive('close')->never();

        $http = new class() extends HttpComponent {
            public function __invoke(Request $request)
            {
                return ['message' => 'Hello world'];
            }
        };

        $http->onError($connection, $exception);

        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_can_handle_request_on_http_error()
    {
        $connection = m::mock(ConnectionInterface::class);
        $exception = new HttpException(500, 'Server not available');

        $connection->shouldReceive('send')->once()->with(m::type('String'))
            ->shouldReceive('close')->once();

        $http = new class() extends HttpComponent {
            public function __invoke(Request $request)
            {
                return ['message' => 'Hello world'];
            }
        };

        $http->onError($connection, $exception);

        $this->addToAssertionCount(1);
    }
}
