<?php

namespace Wrench\Listener;

use Wrench\Connection;
use Wrench\Server;
use Wrench\Test\BaseTest;

class RateLimiterTest extends BaseTest
{
    public function testConstructor(): void
    {
        $instance = self::getInstance();
        $this->assertInstanceOfClass($instance, 'No constructor arguments');
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testListen(): void
    {
        $instance = self::getInstance([]);
        $server = $this->createMock(Server::class);
        $instance->listen($server);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testOnSocketConnect(): void
    {
        $handle = \tmpfile();

        try {
            self::getInstance()->onSocketConnect($handle, $this->getConnection());
        } finally {
            \fclose($handle);
        }
    }

    protected function getConnection(): Connection
    {
        $connection = $this->createMock(Connection::class);

        $connection
            ->expects($this->any())
            ->method('getIp')
            ->will($this->returnValue('127.0.0.1'));

        $connection
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('abcdef01234567890'));

        $manager = $this->createMock('\Wrench\ConnectionManager');
        $manager->expects($this->any())->method('count')->will($this->returnValue(5));

        $connection
            ->expects($this->any())
            ->method('getConnectionManager')
            ->will($this->returnValue($manager));

        return $connection;
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testOnSocketDisconnect(): void
    {
        $handle = \tmpfile();

        try {
            self::getInstance()->onSocketDisconnect($handle, $this->getConnection());
        } finally {
            \fclose($handle);
        }
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testOnClientData(): void
    {
        $handle = \tmpfile();

        try {
            self::getInstance()->onClientData($handle, $this->getConnection());
        } finally {
            \fclose($handle);
        }
    }
}
