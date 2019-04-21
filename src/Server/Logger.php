<?php

namespace Swarm\Server;

use Exception;
use Illuminate\Support\Traits\ForwardsCalls;
use Laravie\Stream\Log\Console;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Logger
{
    use ForwardsCalls;

    /**
     * Console output.
     *
     * @var \Laravie\Stream\Log\Console
     */
    protected $output;

    /**
     * Log verbosity.
     *
     * @var int
     */
    protected $verbosity = OutputInterface::VERBOSITY_NORMAL;

    /**
     * Construct a new logger.
     *
     * @param \Laravie\Stream\Log\Console $output
     */
    public function __construct(Console $output)
    {
        $this->output = $output;
    }

    /**
     * Get logger verbosity.
     *
     * @return int
     */
    public function getVerbosity()
    {
        return $this->verbosity;
    }

    /**
     * Set logger verbosity.
     *
     * @param int $level
     *
     * @return $this
     */
    public function setVerbosity(int $level)
    {
        $this->verbosity = $level;

        return $this;
    }

    /**
     * Set logger from console output.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return $this
     */
    public function fromConsoleOutput(OutputInterface $output)
    {
        $this->setVerbosity($output->getVerbosity());

        return $this;
    }

    /**
     * Log connection state update.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param string                       $state
     *
     * @return void
     */
    public function onConnectionUpdate(ConnectionInterface $connection, string $state): void
    {
        $socketId = $connection->socketId ?? 'null';

        $this->output->warn(\sprintf('[Conn:%s] %s.', $socketId, $state));
    }

    /**
     * Log on message broadcast.
     *
     * @param \Ratchet\ConnectionInterface $connection
     * @param string                       $message
     *
     * @return void
     */
    public function onMessageBroadcast(ConnectionInterface $connection, string $message): void
    {
        if ($this->verbosity === OutputInterface::VERBOSITY_DEBUG) {
            $socketId = $connection->socketId ?? 'null';

            $this->output->info("[Conn:{$socketId}] broadcast message: {$message}.");
        }
    }

    /**
     * Log on message received.
     *
     * @param \Ratchet\ConnectionInterface                $connection
     * @param \Ratchet\RFC6455\Messaging\MessageInterface $message
     *
     * @return void
     */
    public function onMessageReceived(ConnectionInterface $connection, MessageInterface $message): void
    {
        if ($this->verbosity === OutputInterface::VERBOSITY_DEBUG) {
            $socketId = $connection->socketId ?? 'null';

            $this->output->info("[Conn:{$socketId}] received message: {$message->getPayload()}.");
        }
    }

    /**
     * Log error has occured.
     *
     * @param \Exception $exception
     *
     * @return void
     */
    public function onError(Exception $exception): void
    {
        $exceptionClass = \get_class($exception);

        $message = "Exception `{$exceptionClass}` thrown: `{$exception->getMessage()}`.";

        if ($this->verbosity === OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $message .= $exception->getTraceAsString();
        }

        $this->output->error($message);
    }

    /**
     * Passthrough method call to Laravie\Stream\Log\Console.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @throws \BadMethodException if method doesn't exist
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->forwardCallTo($this->output, $method, $parameters);
    }
}
