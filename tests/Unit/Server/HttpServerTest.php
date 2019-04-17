<?php

namespace Swarm\Tests\Unit\Server;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Ratchet\Http\HttpRequestParser;
use Ratchet\Http\HttpServerInterface;
use Swarm\Server\HttpServer;

class HttpServerTest extends TestCase
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
        $http = m::mock(HttpServerInterface::class);

        $server = new HttpServer($http, 1024);

        $refl = new \ReflectionObject($server);
        $reflReqParser = $refl->getProperty('_reqParser');
        $reflReqParser->setAccessible(true);

        $reqParser = $reflReqParser->getValue($server);

        $this->assertInstanceOf(HttpRequestParser::class, $reqParser);
        $this->assertSame(1024, $reqParser->maxSize);
    }
}
