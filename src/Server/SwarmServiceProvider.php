<?php

namespace Swarm\Server;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use React\EventLoop\Factory;
use React\Stream\WritableResourceStream;
use React\Stream\WritableStreamInterface;
use React\EventLoop\LoopInterface;

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

        $this->registerCoreContainerAliases();
    }

    /**
     * Register the core class aliases in the container.
     *
     * @return void
     */
    protected function registerCoreContainerAliases(): void
    {
        $this->app->bind(LoopInterface::class, function (Application $app) {
            return $app->make('swarm.event-loop');
        });

        $this->app->bind(WritableStreamInterface::class, function (Application $app) {
            return $app->make('swarm.stream-writer');
        });
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\StartWebSocketServer::class,
            ]);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'swarm.event-loop',
            LoopInterface::class,
            'swarm.stream-writer',
            WritableStreamInterface::class,
        ];
    }
}
