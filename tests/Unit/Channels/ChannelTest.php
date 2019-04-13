<?php

namespace Swarm\Tests\Unit\Channels;

use Mockery as m;
use Swarm\Channels\Channel;
use PHPUnit\Framework\TestCase;
use Ratchet\ConnectionInterface;

class ChannelTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_has_proper_signature()
    {
        $channel = new Channel('foo');

        $this->assertSame('foo', $channel->id());
    }

    /** @test */
    public function it_can_subscribe_and_unsubscribe_connections()
    {
        $channel = new Channel('foo');

        $subscriber1 = m::mock(ConnectionInterface::class);
        $subscriber2 = m::mock(ConnectionInterface::class);

        $subscriber1->socketId = 'subscriber1';
        $subscriber2->socketId = 'subscriber2';

        $channel->subscribe($subscriber1);
        $channel->subscribe($subscriber2);

        $this->assertEquals(2, count($channel));
        $this->assertSame($channel, $subscriber1->channels['foo']);
        $this->assertSame($channel, $subscriber2->channels['foo']);

        $channel->unsubscribe($subscriber1);

        $this->assertEquals(1, count($channel));
        $this->assertEmpty($subscriber1->channels);
        $this->assertSame($channel, $subscriber2->channels['foo']);
    }

    /** @test */
    public function it_can_broadcast_to_subscribed_connections()
    {
        $payload = [
            'event' => 'hello',
            'data' => 'Welcom subscriber',
        ];

        $channel = new Channel('foo');

        $subscriber1 = m::mock(ConnectionInterface::class);
        $subscriber2 = m::mock(ConnectionInterface::class);

        $subscriber1->socketId = 'subscriber1';
        $subscriber2->socketId = 'subscriber2';

        $subscriber1->shouldReceive('send')->once()->with(json_encode($payload))->andReturnNull();
        $subscriber2->shouldNotReceive('send');

        $channel->subscribe($subscriber1);

        $this->assertNull($channel->broadcast($payload));
    }
}
