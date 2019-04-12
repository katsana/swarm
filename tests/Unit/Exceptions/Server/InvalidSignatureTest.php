<?php

namespace Swarm\Tests\Unit\Exceptions\Server;

use PHPUnit\Framework\TestCase;
use Swarm\Exceptions\Server\InvalidSignature;

class InvalidSignatureTest extends TestCase
{
    /** @test */
    public function it_has_proper_signature()
    {
        $exception = new InvalidSignature('foo');

        $this->assertInstanceOf('Swarm\Exceptions\Server\WebSocketException', $exception);

        $this->assertSame([
            'event' => 'error',
            'data' => [
                'message' => 'Invalid Signature',
                'code' => 4009,
            ],
        ], $exception->getPayload());
    }

    /** @test */
    public function it_throws_proper_exception()
    {
        $this->expectException(InvalidSignature::class);
        $this->expectExceptionMessage('Invalid Signature');
        $this->expectExceptionCode(4009);

        throw new InvalidSignature('foo');
    }
}
