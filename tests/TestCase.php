<?php

namespace Swarm\Tests;

use Orchestra\Testbench\TestCase as Testing;

abstract class TestCase extends Testing
{
    /**
     * Get package aliases.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Swarm' => 'Swarm\Router',
        ];
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'Swarm\SwarmServiceProvider',
            'Swarm\Server\SwarmServiceProvider',
        ];
    }
}
