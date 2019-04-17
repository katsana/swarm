<?php

namespace Swarm\Tests\Unit\Server;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Swarm\Server\QueryParameters;
use Psr\Http\Message\RequestInterface;

class QueryParametersTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can()
    {
        $request = m::mock(RequestInterface::class);

        $request->shouldReceive('getUri->getQuery')->andReturn('foo=bar&hello=world');

        $query = QueryParameters::create($request);

        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $query->all());
        $this->assertSame('bar', $query->get('foo'));
        $this->assertSame('world', $query->get('hello'));
        $this->assertNull($query->get('foobar'));

    }
}
