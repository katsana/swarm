<?php

namespace Swarm\Tests\Feature;

use Swarm\Router;
use Swarm\Tests\TestCase;

class MinionTest extends TestCase
{
    /** @test */
    public function it_can_resolve_the_facade()
    {
        $router = Router::getFacadeRoot();

        $this->assertInstanceOf('Swarm\Server\Router', $router);
    }
}
