<?php

namespace Swarm\Channels;

use Countable;
use Ratchet\ConnectionInterface;

class Channel implements Countable
{
    /**
     * Channel ID.
     *
     * @var string
     */
    protected $id;

    /**
     * Channel subscribers.
     *
     * @var array
     */
    protected $subscribers = [];

    /**
     * Construct a new channel.
     *
     * @param string $channelId
     */
    public function __construct(string $channelId)
    {
        $this->id = $channelId;
    }

    /**
     * Get channel id.
     *
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Subscribe to channel.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return $this
     */
    public function subscribe(ConnectionInterface $connection)
    {
        $connection->channels[$this->id()] = $this;

        $this->subscribers[$connection->socketId] = $connection;

        return $this;
    }

    /**
     * Unsubscribe the connection.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return void
     */
    public function unsubscribe(ConnectionInterface $connection): void
    {
        unset(
            $this->subscribers[$connection->socketId], $connection->channels[$this->id()]
        );
    }

    /**
     * Broadcast payload to subscriber.
     *
     * @param array $payload
     *
     * @return void
     */
    public function broadcast(array $payload): void
    {
        foreach ($this->subscribers as $subscriber) {
            $subscriber->send(\json_encode($payload));
        }
    }

    /**
     * Count available subscribers.
     *
     * @return int
     */
    public function count()
    {
        return \count($this->subscribers);
    }
}
