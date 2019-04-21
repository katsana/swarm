<?php

namespace Swarm\Tests\Unit\Server;

use Laravie\Stream\Log\Console;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Swarm\Server\Logger;
use Symfony\Component\Console\Output\OutputInterface;

class LoggerTest extends TestCase
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
        $console = m::mock(Console::class);

        $logger = new Logger($console);

        $this->assertSame(OutputInterface::VERBOSITY_NORMAL, $logger->getVerbosity());
    }
}
