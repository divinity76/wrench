<?php

namespace Wrench\Listener;

use Wrench\Server;
use Wrench\Test\BaseTest;

/**
 * Payload test.
 */
abstract class ListenerBaseTest extends BaseTest
{
    /**
     * @depends testConstructor
     *
     * @doesNotPerformAssertions
     */
    public function testListen(ListenerInterface $instance): void
    {
        $server = $this->createMock(Server::class);
        $instance->listen($server);
    }

    abstract public function testConstructor();
}
