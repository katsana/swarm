<?php


use Mockery as m;
use Swarm\Channels\Channel;
use Swarm\Channels\Manager;
use PHPUnit\Framework\TestCase;
use Ratchet\ConnectionInterface;

class ManagerTest extends TestCase
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
        $manager = new Manager;

        $this->assertSame(0, count($manager));
    }

    /** @test */
    public function it_can_subscribe_and_unsubscribe_connections()
    {
        $manager = new Manager();

        $subscriber1 = m::mock(ConnectionInterface::class);
        $subscriber2 = m::mock(ConnectionInterface::class);
        $subscriber3 = m::mock(ConnectionInterface::class);

        $subscriber1->socketId = 'subscriber1';
        $subscriber2->socketId = 'subscriber2';
        $subscriber3->socketId = 'subscriber3';

        $manager->subscribe('1', $subscriber1);
        $manager->subscribe('1', $subscriber2);
        $manager->subscribe('2', $subscriber3);

        $this->assertEquals(2, count($manager));
        $this->assertInstanceOf(Channel::class, $subscriber1->channels['1']);
        $this->assertInstanceOf(Channel::class, $subscriber2->channels['1']);
        $this->assertInstanceOf(Channel::class, $subscriber3->channels['2']);

        $manager->unsubscribe($subscriber1);

        $this->assertEquals(2, count($manager));
        $this->assertEmpty($subscriber1->channels);
        $this->assertInstanceOf(Channel::class, $subscriber2->channels['1']);
        $this->assertInstanceOf(Channel::class, $subscriber3->channels['2']);

        $manager->unsubscribe($subscriber2);

        $this->assertEquals(1, count($manager));
        $this->assertEmpty($subscriber1->channels);
        $this->assertEmpty($subscriber2->channels);
        $this->assertInstanceOf(Channel::class, $subscriber3->channels['2']);
    }

    /** @test */
    public function it_can_broadcast_to_subscribed_connections()
    {
        $payload = [
            'event' => 'hello',
            'data' => 'Welcom subscriber',
        ];

        $manager = new Manager();

        $subscriber1 = m::mock(ConnectionInterface::class);
        $subscriber2 = m::mock(ConnectionInterface::class);
        $subscriber3 = m::mock(ConnectionInterface::class);

        $subscriber1->socketId = 'subscriber1';
        $subscriber2->socketId = 'subscriber2';
        $subscriber3->socketId = 'subscriber3';

        $subscriber1->shouldReceive('send')->once()->with(json_encode($payload))->andReturnNull();
        $subscriber2->shouldReceive('send')->once()->with(json_encode($payload))->andReturnNull();
        $subscriber3->shouldNotReceive('send');

        $manager->subscribe('1', $subscriber1);
        $manager->subscribe('1', $subscriber2);
        $manager->subscribe('2', $subscriber3);

        $this->assertNull($manager->broadcast('1', $payload));
    }
}
