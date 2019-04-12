<?php

namespace Swarm\Tests\Feature;

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
        $this->assertInstanceOf('React\EventLoop\LoopInterface', $this->app['swarm.event-loop']);
        $this->assertInstanceOf('React\Stream\WritableStreamInterface', $this->app['swarm.stream-writer']);
    }
}
