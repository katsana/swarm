<?php

namespace Swarm\Tests\Unit\Server;

use Mockery as m;
use Swarm\Server\Logger;
use Swarm\Server\Connector;
use React\EventLoop\Factory;
use PHPUnit\Framework\TestCase;
use Ratchet\Http\Router;

class ConnectorTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_create_unsecured_server()
    {
        $eventLoop = Factory::create();
        $logger = m::mock(Logger::class);
        $container = m::mock(Container::class);
        $router = m::mock(Router::class);

        $hostname = '0.0.0.0:8085';
        $logger->shouldReceive('info')->with("Server running at http://{$hostname}\n")->andReturnNull();

        $connector = new Connector($hostname, $eventLoop, $logger);
        $connector->handle($router, ['server' => ['secure' => false]]);

        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_can_create_secured_server()
    {
        $eventLoop = Factory::create();
        $logger = m::mock(Logger::class);
        $container = m::mock(Container::class);
        $router = m::mock(Router::class);

        $hostname = '0.0.0.0:8086';
        $logger->shouldReceive('info')->with("Server running at https://{$hostname}\n")->andReturnNull();

        $connector = new Connector($hostname, $eventLoop, $logger);
        $connector->handle($router, ['server' => ['secure' => true]]);

        $this->addToAssertionCount(1);
    }
}
