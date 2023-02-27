<?php

namespace Wrench\Socket;

use Wrench\Exception\SocketException;

class ServerClientSocketTest extends SocketBaseTest
{
    public function testConstructor(): void
    {
        $instance = self::getInstance(null);
        $this->assertInstanceOfClass($instance);
    }

    public function testIsConnected(): void
    {
        $instance = self::getInstance(null);
        $connected = $instance->isConnected();
        self::assertTrue(\is_bool($connected), 'isConnected returns boolean');
        self::assertFalse($connected);
    }

    public function testGetIpTooSoon(): void
    {
        $instance = self::getInstance(null);
        $this->expectException(SocketException::class);

        $instance->getIp();
    }

    public function testGetPortTooSoon(): void
    {
        $instance = self::getInstance(null);
        $this->expectException(SocketException::class);

        $instance->getPort();
    }
}
