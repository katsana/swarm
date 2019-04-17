<?php

namespace Swarm\Channels;

use Countable;
use Ratchet\ConnectionInterface;

class Manager implements Countable
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
            $this->channels[$channelId] = $this->newChannel($channelId);
        }

        $this->channels[$channelId]->subscribe($connection);
    }

    /**
     * Unsubscribe from all channels.
     *
     * @param \Rachet\ConnectionInterface $connection
     *
     * @return void
     */
    public function unsubscribe(ConnectionInterface $connection): void
    {
        if (! isset($connection->channels)) {
            return;
        }

        $this->unsubscribeFrom($connection->channels, $connection);
    }

    /**
     * Unsubcribe on explicit channels.
     *
     * @param iterable            $channelIds
     * @param ConnectionInterface $connection
     *
     * @return void
     */
    public function unsubscribeFrom(iterable $channelIds, ConnectionInterface $connection): void
    {
        foreach ($channelIds as $channelId) {
            $channel = $this->channels[$channelId] ?? null;

            if (! \is_null($channel)) {
                $channel->unsubscribe($connection);

                if (\count($channel) === 0) {
                    unset($this->channels[$channelId]);
                }
            }
        }
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

    /**
     * Count available subscribers.
     *
     * @return int
     */
    public function count()
    {
        return \count($this->channels);
    }

    /**
     * Create a new channel.
     *
     * @param string $channelId
     *
     * @return \Swarm\Channels\Channel
     */
    protected function newChannel(string $channelId)
    {
        return new Channel($channelId);
    }
}
