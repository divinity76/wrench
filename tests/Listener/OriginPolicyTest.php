<?php

namespace Wrench\Listener;

use Wrench\Connection;
use Wrench\Server;
use Wrench\Test\BaseTest;

class OriginPolicyTest extends BaseTest
{
    public function testConstructor(): void
    {
        $instance = self::getInstance([]);
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
     * @dataProvider getValidArguments
     */
    public function testValidAllowed(array $allowed, string $domain): void
    {
        $instance = self::getInstance($allowed);
        self::assertTrue($instance->isAllowed($domain));
    }

    /**
     * @dataProvider getValidArguments
     */
    public function testValidHandshake(array $allowed, string $domain): void
    {
        $instance = self::getInstance($allowed);

        $connection = $this->createMock(Connection::class);

        $connection
            ->expects($this->never())
            ->method('close');

        $instance->onHandshakeRequest($connection, '/', $domain, 'abc', []);
    }

    /**
     * @dataProvider getInvalidArguments
     *
     * @param array  $allowed
     * @param string $badDomain
     */
    public function testInvalidAllowed(array $allowed, string $badDomain): void
    {
        $instance = self::getInstance($allowed);
        self::assertFalse($instance->isAllowed($badDomain));
    }

    /**
     * @dataProvider getInvalidArguments
     */
    public function testInvalidHandshake(array $allowed, string $badDomain): void
    {
        $instance = self::getInstance($allowed);

        $connection = $this->createMock(Connection::class);

        $connection
            ->expects($this->once())
            ->method('close');

        $instance->onHandshakeRequest($connection, '/', $badDomain, 'abc', []);
    }

    public static function getValidArguments(): array
    {
        return [
            [['localhost'], 'http://localhost'],
            [['foobar.com'], 'https://foobar.com'],
            [['https://foobar.com'], 'https://foobar.com'],
        ];
    }

    public static function getInvalidArguments(): array
    {
        return [
            [['localhost'], 'localdomain'],
            [['foobar.com'], 'foobar.org'],
            [['https://foobar.com'], 'http://foobar.com'],
            [['http://foobar.com'], 'foobar.com'],
        ];
    }
}
