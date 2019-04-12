<?php

namespace Swarm\Tests\Unit\Exceptions\Server;

use PHPUnit\Framework\TestCase;
use Swarm\Exceptions\Server\InvalidConnection;

class InvalidConnectionTest extends TestCase
{
    /** @test */
    public function it_has_proper_signature()
    {
        $exception = new InvalidConnection('foo');

        $this->assertInstanceOf('Swarm\Exceptions\Server\WebSocketException', $exception);

        $this->assertSame([
            'event' => 'error',
            'data' => [
                'message' => 'Invalid Connection',
                'code' => 4009,
            ],
        ], $exception->getPayload());
    }

    /** @test */
    public function it_throws_proper_exception()
    {
        $this->expectException(InvalidConnection::class);
        $this->expectExceptionMessage('Invalid Connection');
        $this->expectExceptionCode(4009);

        throw new InvalidConnection('foo');
    }
}
