<?php

namespace Wrench\Protocol;

/**
 * @see http://tools.ietf.org/html/draft-ietf-hybi-thewebsocketprotocol-10
 */
class Hybi10Protocol extends HybiProtocol
{
    private const VERSION = 10;

    public function getVersion(): int
    {
        return self::VERSION;
    }

    public function acceptsVersion(int $version): bool
    {
        return $version === self::VERSION;
    }
}
