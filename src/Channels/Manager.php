<?php

namespace Swarm\Channels;

use Ratchet\ConnectionInterface;

class Manager
{
    /**
     * List of channels.
     *
     * @var array
     */
    protected $channels = [];

    /**
     * Subscribe to channel.
     *
     * @param string                      $channelId
     * @param \Rachet\ConnectionInterface $connection
     *
     * @throws \Swarm\Exceptions\Server\InvalidChannel
     *
     * @return void
     */
    public function subscribe(string $channelId, ConnectionInterface $connection): void
    {
        if (! isset($this->channels[$channelId])) {
            $this->channels[$channelId] = new Channel($channelId);
        }

        $this->channels[$channelId]->subscribe($connection);
    }

    /**
     * Unsubscribe from channel.
     *
     * @param \Rachet\ConnectionInterface $connection
     *
     * @return void
     */
    public function unsubscribe(ConnectionInterface $connection)
    {
        if (! isset($connection->channel)) {
            return;
        }

        $channelIds = Collection::make($connection->channels)->transform(function ($connection) {
            return $connection->id();
        })->each(function ($channelId) {
            $channel = $this->channels[$channelId] ?? null;

            if (! \is_null($channel)) {
                $channel->unsubscribe($connection);

                if (\count($channel) === 0) {
                    unset($this->channels[$channelId]);
                }
            }
        });
    }

    /**
     * Broadcast payload to subscriber.
     *
     * @param string $channelId
     * @param array  $payload
     *
     * @return void
     */
    public function broadcast(string $channelId, array $payload): void
    {
        if (isset($this->channels[$channelId])) {
            $this->channels[$channelId]->broadcast($payload);
        }
    }
}
