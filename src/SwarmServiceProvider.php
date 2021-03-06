<?php

namespace Swarm;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Laravie\Stream\Log\Console as ConsoleLogger;
use Swarm\Server\Logger;
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
        $this->app->singleton('swarm.router', static function (Container $app) {
            return new Server\Router($app, new RouteCollection());
        });

        $this->app->singleton('swarm.logger', static function (Container $app) {
            return new Logger($app->make(ConsoleLogger::class));
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

        if ($this->app->runningInConsole()) {
            $this->commands([
                Server\Console\StartWebSocketServer::class,
            ]);
        }
    }
}
