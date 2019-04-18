<?php

namespace Swarm;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use JakubOnderka\PhpConsoleColor\ConsoleColor;
use React\Stream\WritableStreamInterface;
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
        $this->app->singleton('swarm.router', function (Application $app) {
            return new Server\Router($app, new RouteCollection());
        });

        $this->app->singleton('swarm.logger', function (Application $app) {
            return new Logger($app->make(WritableStreamInterface::class), new ConsoleColor());
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
