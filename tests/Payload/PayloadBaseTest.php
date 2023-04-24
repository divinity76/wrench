<?php

namespace Wrench\Payload;

use Wrench\Protocol\Protocol;
use Wrench\Socket\ClientSocket;
use Wrench\Test\BaseTest;

abstract class PayloadBaseTest extends BaseTest
{
    private Payload $payload;

    /**
     * @see PHPUnit_Payloadwork_TestCase::setUp()
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->payload = self::getInstance();
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOfClass(self::getInstance());
    }

    /**
     * @dataProvider getValidEncodePayloads
     */
    public function testBijection(int $type, string $payload): void
    {
        // Encode the payload
        $this->payload->encode($payload, $type);

        // Create a new payload and read the data in with encode
        $payload = self::getInstance();
        $payload->encode($this->payload->getPayload(), $type);

        // These still match
        self::assertEquals(
            $this->payload->getType(),
            $payload->getType(),
            'Types match after encode -> receiveData'
        );

        self::assertEquals(
            $this->payload->getPayload(),
            $payload->getPayload(),
            'Payloads match after encode -> receiveData'
        );
    }

    /**
     * @dataProvider getValidEncodePayloads
     */
    public function testEncodeTypeReflection(int $type, string $payload): void
    {
        $this->payload->encode($payload, Protocol::TYPE_TEXT);
        self::assertEquals(Protocol::TYPE_TEXT, $this->payload->getType(), 'Encode retains type information');
    }

    /**
     * @dataProvider getValidEncodePayloads
     */
    public function testEncodePayloadReflection(int $type, string $payload): void
    {
        $this->payload->encode($payload, Protocol::TYPE_TEXT);
        self::assertEquals($payload, $this->payload->getPayload(), 'Encode retains payload information');
    }

    /**
     * @dataProvider getValidEncodePayloads
     */
    public function testSendToSocket(int $type, string $payload): void
    {
        $socket = $this->getMockBuilder(ClientSocket::class)
            ->onlyMethods(['getIp', 'getPort', 'isConnected', 'send'])
            ->disableOriginalConstructor()
            ->getMock();

        $failedSocket = $this->getMockBuilder(ClientSocket::class)
            ->onlyMethods(['getIp', 'getPort', 'isConnected', 'send'])
            ->disableOriginalConstructor()
            ->getMock();

        $socket->expects($this->any())
            ->method('send')
            ->will($this->returnValue(500));

        $failedSocket->expects($this->any())
            ->method('send')
            ->will($this->returnValue(null));

        $this->payload->encode($payload, $type);

        self::assertTrue($this->payload->sendToSocket($socket));
        self::assertFalse($this->payload->sendToSocket($failedSocket));
    }

    /**
     * @dataProvider getValidEncodePayloads
     *
     * @doesNotPerformAssertions
     */
    public function testReceieveData(int $type, string $payload): void
    {
        $payload = self::getInstance();
        $payload->receiveData($payload);
    }

    public static function getValidEncodePayloads(): array
    {
        return [
            [
                Protocol::TYPE_TEXT,
                "123456\x007890!@#$%^&*()qwe\trtyuiopQWERTYUIOPasdfghjklASFGH\n
                JKLzxcvbnmZXCVBNM,./<>?;[]{}-=_+\|'asdad0x11\aasdassasdasasdsd",
            ],
            [
                Protocol::TYPE_TEXT,
                \pack('CCCCCCC', 0x00, 0x01, 0x02, 0x03, 0x04, 0xFF, 0xF0),
            ],
            [
                Protocol::TYPE_TEXT,
                ' ',
            ],
        ];
    }
}
