<?php

namespace Swarm\Tests\Unit\Server;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Ratchet\Http\Router;
use React\EventLoop\Factory;
use Swarm\Server\Connector;
use Swarm\Server\Logger;

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
        $connector->handle($router, ['secure' => false]);

        $this->addToAssertionCount(1);

        $eventLoop->stop();
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
        $connector->handle($router, ['secure' => true]);

        $this->addToAssertionCount(1);

        $eventLoop->stop();
    }
}
