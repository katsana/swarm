<?php

namespace Swarm\Server;

use Ratchet\Http\HttpServer;
use Ratchet\Http\Router as RatchetRouter;
use Ratchet\Server\IoServer;
use React\EventLoop\LoopInterface;
use React\Socket\Server as SocketServer;
use React\Socket\ServerInterface;

class Connector
{
    /**
     * The server hostname.
     *
     * @var string
     */
    protected $hostname;

    /**
     * The event loop implementation.
     *
     * @var \React\EventLoop\LoopInterface
     */
    protected $eventLoop;

    /**
     * The console logger.
     *
     * @var \Swarm\Server\Logger
     */
    protected $logger;

    /**
     * Construct a new HTTP Server connector.
     *
     * @param string                         $hostname
     * @param \React\EventLoop\LoopInterface $eventLoop
     * @param \Swarm\Server\Logger           $logger
     */
    public function __construct(string $hostname, LoopInterface $eventLoop, Logger $logger)
    {
        $this->hostname = $hostname;
        $this->eventLoop = $eventLoop;
        $this->logger = $logger;
    }

    /**
     * Create IO Server.
     *
     * @param \Ratchet\Http\Router $router
     * @param array                $config
     *
     * @return \Ratchet\Server\IoServer
     */
    public function handle(RatchetRouter $router, array $config): IoServer
    {
        if (($config['secure'] ?? false) === true) {
            $socket = $this->bootSecuredServer($config['options'] ?? []);
        } else {
            $socket = $this->bootUnsecuredServer();
        }

        $app = new CheckOrigin($router, $config['allowed_origins'] ?? []);
        $http = new HttpServer($app, ($config['max_request_size_in_kb'] ?? 4) * 1024);

        return new IoServer($http, $socket, $this->eventLoop);
    }

    /**
     * Boot HTTPS Server.
     *
     * @param array $options
     *
     * @return \React\Socket\ServerInterface
     */
    protected function bootSecuredServer(array $options): ServerInterface
    {
        $socket = new SocketServer("tls://{$this->hostname}", $this->eventLoop, $options);

        $this->logger->info("Server running at https://{$this->hostname}\n");

        return $socket;
    }

    /**
     * Boot HTTP Server.
     *
     * @return \React\Socket\ServerInterface
     */
    protected function bootUnsecuredServer(): ServerInterface
    {
        $socket = new SocketServer($this->hostname, $this->eventLoop);

        $this->logger->info("Server running at http://{$this->hostname}\n");

        return $socket;
    }
}
