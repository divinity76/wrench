<?php

namespace Wrench\Socket;

use TypeError;
use Socket;

class ServerClientSocket extends AbstractSocket
{
    /**
     * A server client socket is accepted from a listening socket, so there's
     * no need to call ->connect() or whatnot.
     *
     * @param resource|Socket|null $accepted_socket
     */
    public function __construct($accepted_socket, array $options = [])
    {
        if (null !== $accepted_socket && !\is_resource($accepted_socket) && !$accepted_socket instanceof Socket) {
            throw new TypeError(
                sprintf('%s(): Argument #1 ($accepted_socket) must be of type %s, %s given', __METHOD__, 'resource|Socket|null', \get_debug_type($accepted_socket))
            );
        }

        parent::__construct($options);

        $this->socket = $accepted_socket;
        $this->connected = null !== $accepted_socket;
    }
}
