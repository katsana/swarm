<?php

namespace Swarm;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Routing\RouteCollection;

class SwarmServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('swarm.router', function () {
            return new Server\Router(new RouteCollection());
        });
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/swarm.php' => \config_path('config/swarm.php'),
        ], 'config');

        $this->commands([
            Server\Console\StartWebSocketServer::class,
        ]);
    }
}
