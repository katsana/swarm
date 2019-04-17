<?php

namespace Swarm\Server\Console;

use Illuminate\Console\Command;
use Ratchet\Http\Router;
use React\EventLoop\LoopInterface;
use React\Stream\WritableStreamInterface;
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

        $this->laravel->instance(LoopInterface::class, $eventLoop = Factory::create());
        $this->laravel->instance(WritableStreamInterface::class, $writer = new WritableResourceStream(STDOUT, $eventLoop));

        $router = new Router(
            new UrlMatcher($this->laravel['swarm.router']->getRoutes(), new RequestContext())
        );

        $connector = new Connector($hostname, $eventLoop, $writer);

        $server = $connector->handle($router, $config);

        $server->run();
    }
}
