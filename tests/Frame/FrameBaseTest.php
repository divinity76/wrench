<?php

namespace Wrench\Frame;

use Wrench\Protocol\Protocol;
use Wrench\Test\BaseTest;

abstract class FrameBaseTest extends BaseTest
{
    private Frame $frame;

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->frame = self::getNewFrame();
    }

    private function getNewFrame(): Frame
    {
        $class = static::getClass();

        return new $class();
    }

    /**
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->frame);
    }

    /**
     * @dataProvider getValidEncodePayloads
     */
    public function testBijection(int $type, string $payload, bool $masked): void
    {
        // Encode the payload
        $this->frame->encode($payload, $type, $masked);

        // Get the resulting buffer
        $buffer = $this->frame->getFrameBuffer();
        self::assertTrue((bool) $buffer, 'Got raw frame buffer');

        // And feed it back into a new frame
        $frame = self::getNewFrame();
        $frame->receiveData($buffer);

        // Check the properties of the new frame against the old, all match
        self::assertEquals(
            $this->frame->getType(),
            $frame->getType(),
            'Types match after encode -> receiveData'
        );

        self::assertEquals(
            $this->frame->getFramePayload(),
            $frame->getFramePayload(),
            'Payloads match after encode -> receiveData'
        );

        // Masking key should not be different, because we read the buffer in directly
        self::assertEquals(
            $this->frame->getFrameBuffer(),
            $frame->getFrameBuffer(),
            'Raw buffers match too'
        );

        // This time, we create a new frame and read the data in with encode
        $frame = self::getNewFrame();
        $frame->encode($this->frame->getFramePayload(), $type, $masked);

        // These still match
        self::assertEquals(
            $this->frame->getType(),
            $frame->getType(),
            'Types match after encode -> receiveData -> encode'
        );

        self::assertEquals(
            $this->frame->getFramePayload(),
            $frame->getFramePayload(),
            'Payloads match after encode -> receiveData -> encode'
        );

        // But the masking key should be different, thus, so are the buffers
        if ($masked) {
            self::assertNotEquals(
                $this->frame->getFrameBuffer(),
                $frame->getFrameBuffer(),
                'Raw buffers don\'t match because of masking'
            );
        } else {
            self::assertEquals(
                $this->frame->getFramePayload(),
                $frame->getFramePayload(),
                'Payloads match after encode -> receiveData -> encode'
            );
        }
    }

    /**
     * @dataProvider getValidEncodePayloads
     */
    public function testEncodeTypeReflection(int $type, string $payload, bool $masked): void
    {
        $this->frame->encode($payload, $type);
        self::assertEquals(Protocol::TYPE_TEXT, $this->frame->getType(), 'Encode retains type information');
    }

    /**
     * @dataProvider getValidEncodePayloads
     */
    public function testEncodeLengthReflection(int $type, string $payload, bool $masked): void
    {
        $this->frame->encode($payload, $type);
        self::assertEquals(\strlen($payload), $this->frame->getLength(), 'Encode does not alter payload length');
    }

    /**
     * @dataProvider getValidEncodePayloads
     */
    public function testEncodePayloadReflection(int $type, string $payload, bool $masked): void
    {
        $this->frame->encode($payload, $type, $masked);
        self::assertEquals($payload, $this->frame->getFramePayload(), 'Encode retains payload information');
    }

    public static function getValidEncodePayloads(): array
    {
        return [
            [
                Protocol::TYPE_TEXT,
                "123456\x007890!@#$%^&*()qwe\trtyuiopQWERTYUIOPasdfghjklASFGH\n
                JKLzxcvbnmZXCVBNM,./<>?;[]{}-=_+\|'asdad0x11\aasdassasdasasdsd",
                true,
            ],
            [
                Protocol::TYPE_TEXT,
                \pack('CCCCCCC', 0x00, 0x01, 0x02, 0x03, 0x04, 0xFF, 0xF0),
                true,
            ],
            [Protocol::TYPE_TEXT, ' ', true],
        ];
    }
}
