<?php

namespace Wrench\Protocol;

/**
 * This is the version of websockets used by Chrome versions 17 through 19.
 *
 * @see http://tools.ietf.org/html/rfc6455
 */
class Rfc6455Protocol extends HybiProtocol
{
    private const VERSION = 13;

    public function getVersion(): int
    {
        return self::VERSION;
    }

    public function acceptsVersion(int $version): bool
    {
        return $version <= self::VERSION;
    }
}
