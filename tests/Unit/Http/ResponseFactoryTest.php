<?php

namespace Swarm\Tests\Unit\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Ratchet\ConnectionInterface;
use function React\Promise\resolve;
use Swarm\Http\ResponseFactory;
use Swarm\Socket\HttpComponent;

class ResponseFactoryTest extends TestCase
{
    /**
     * Teardown the teest environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @xtest */
    public function it_can_dispatch_a_request()
    {
        $connection = m::mock(ConnectionInterface::class);
        $request = m::mock(Request::class);

        $connection->shouldReceive('send')->once()->andReturnUsing(function ($message) {
            $this->assertInstanceOf(JsonResponse::class, $message);
            $this->assertSame('{"foo":"bar"}', $message->getContent());
        })->shouldReceive('close')->once()->andReturnNull();

        $factory = new ResponseFactory($connection, $request);

        $response = $factory(new class() extends HttpComponent {
            public function __invoke(Request $request)
            {
                return ['foo' => 'bar'];
            }
        });
    }

    /** @test */
    public function it_can_dispatch_a_promised_request()
    {
        $connection = m::mock(ConnectionInterface::class);
        $request = m::mock(Request::class);

        $connection->shouldReceive('send')->once()->andReturnUsing(function ($message) {
            $this->assertInstanceOf(JsonResponse::class, $message);
            $this->assertSame('{"foo":"foobar"}', $message->getContent());
        })->shouldReceive('close')->once()->andReturnNull();

        $factory = new ResponseFactory($connection, $request);

        $response = $factory(new class() extends HttpComponent {
            public function __invoke(Request $request)
            {
                return resolve(['foo' => 'foobar']);
            }
        });
    }
}
