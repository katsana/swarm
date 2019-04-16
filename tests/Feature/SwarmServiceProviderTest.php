<?php

namespace Swarm\Tests\Feature;

use React\EventLoop\LoopInterface;
use React\Stream\WritableStreamInterface;
use Swarm\Tests\TestCase;

class SwarmServiceProviderTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        //
    }

    /** @test */
    public function it_register_the_services()
    {
        $this->assertInstanceOf('Swarm\Server\Router', $this->app['swarm.router']);
        $this->assertInstanceOf(LoopInterface::class, $this->app[LoopInterface::class]);
        $this->assertInstanceOf(WritableStreamInterface::class, $this->app[WritableStreamInterface::class]);
    }
}
