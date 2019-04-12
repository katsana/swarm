<?php

namespace Swarm\Server;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use React\EventLoop\Factory;
use React\Stream\WritableResourceStream;

class SwarmServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('swarm.event-loop', function () {
            return Factory::create();
        });

        $this->app->singleton('swarm.stream-writer', function (Application $app) {
            return new WritableResourceStream(STDOUT, $app['swarm.event-loop']);
        });
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            Console\StartWebSocketServer::class,
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['swarm.event-loop', 'swarm.stream-writer'];
    }
}
