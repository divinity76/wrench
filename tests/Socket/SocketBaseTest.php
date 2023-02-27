<?php

namespace Wrench\Socket;

use Wrench\Test\BaseTest;

abstract class SocketBaseTest extends BaseTest
{
    /**
     * @dataProvider getValidNames
     */
    public function testGetNamePart(string $name, string $ip, string $port): void
    {
        self::assertEquals($ip, AbstractSocket::getNamePart($name, AbstractSocket::NAME_PART_IP), 'splits ip correctly');
        self::assertEquals($port, AbstractSocket::getNamePart($name, AbstractSocket::NAME_PART_PORT), 'splits port correctly');
    }

    public static function getValidNames(): array
    {
        return [
            ['127.0.0.1:52339', '127.0.0.1', '52339'],
            ['255.255.255.255:1025', '255.255.255.255', '1025'],
            ['::1:56670', '::1', '56670'],
        ];
    }
}
