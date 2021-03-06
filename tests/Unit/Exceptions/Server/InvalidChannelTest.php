<?php

namespace Swarm\Tests\Unit\Exceptions\Server;

use PHPUnit\Framework\TestCase;
use Swarm\Exceptions\Server\InvalidChannel;

class InvalidChannelTest extends TestCase
{
    /** @test */
    public function it_has_proper_signature()
    {
        $exception = new InvalidChannel('foo');

        $this->assertInstanceOf('Swarm\Exceptions\Server\WebSocketException', $exception);

        $this->assertSame([
            'event' => 'error',
            'data' => [
                'message' => 'Could not find channel `foo`.',
                'code' => 4001,
            ],
        ], $exception->getPayload());
    }

    /** @test */
    public function it_throws_proper_exception()
    {
        $this->expectException(InvalidChannel::class);
        $this->expectExceptionMessage('Could not find channel `foo`.');
        $this->expectExceptionCode(4001);

        throw new InvalidChannel('foo');
    }
}
