<?php

namespace Swarm\Server\Console;

use Illuminate\Console\Command;
use Laravie\Stream\Log\Console as ConsoleLogger;
use Laravie\Stream\Logger;
use Ratchet\Http\Router;
use React\EventLoop\LoopInterface;
use Swarm\Server\Connector;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class StartWebSocketServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swarm:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the WebSocket Server';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $config = $this->laravel->get('config')->get('swarm', [
            'server' => ['host' => '0.0.0.0', 'port' => 8085, 'secure' => false],
        ]);

        $hostname = "{$config['server']['host']}:{$config['server']['port']}";

        $eventLoop = $this->laravel->make(LoopInterface::class);
        $logger = $this->laravel->make(ConsoleLogger::class);

        $this->laravel->instance(Logger::class, $logger);

        $router = new Router(
            new UrlMatcher($this->laravel->make('swarm.router')->getRoutes(), new RequestContext())
        );

        $connector = new Connector($hostname, $eventLoop, $logger);

        $server = $connector->handle($router, $config);

        $server->run();
    }
}
