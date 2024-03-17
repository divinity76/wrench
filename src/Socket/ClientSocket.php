<?php

namespace Wrench\Socket;

/**
 * Options:
 *  - timeout_connect      => int, seconds, default 2.
 */
class ClientSocket extends UriSocket
{
    /**
     * Default connection timeout.
     *
     * @var int seconds
     */
    public const TIMEOUT_CONNECT = 2;

    public function reconnect(): void
    {
        $this->disconnect();
        $this->connect();
    }

    /**
     * Connects to the given socket.
     */
    public function connect(): bool
    {
        if ($this->isConnected()) {
            return true;
        }
if(PHP_VERSION_ID >= 80300){
    sleep(3);
}
$uri = $this->getUri();
$uridata = parse_url($uri);
        $errno = null;
        $errstr = null;
$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if($sock === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . PHP_EOL;
} else {
    echo "socket_create() OK" . PHP_EOL;
}
var_dump(shell_exec("curl --connect-timeout 1 --max-time 2 -v http://localhost:". ($uridata['port']) ." 2>&1"));
if(1){
    $bind = socket_bind($sock, '0', 0);
if($bind === false) {
    echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . PHP_EOL;
} else {
    echo "socket_bind() OK" . PHP_EOL;
}
     }
socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 3, 'usec' => 0]);
socket_set_option($sock, SOL_SOCKET, SO_SNDTIMEO, ['sec' => 3, 'usec' => 0]);

var_dump($uridata);
$uridata['host'] = '127.0.0.1';
        $t = microtime(true);
$connect = socket_connect($sock, $uridata['host'], $uridata['port']);
        $t = microtime(true) - $t;
echo "socket_connect time: $t\n";
if($connect === false) {
    echo "socket_connect() failed: reason: " . socket_strerror(socket_last_error($sock)) . PHP_EOL;
} else {
    echo "socket_connect() OK" . PHP_EOL;
}
socket_close($sock);
        // Supress PHP error, we're handling it
$attempts = 0;
for(;;){
    ++$attempts;
$this->socket = @\stream_socket_client(
    $this->getUri(),
    $errno,
    $errstr,
    $this->options['timeout_connect'],
    \STREAM_CLIENT_CONNECT,
    $this->getStreamContext()
);
    if($this->socket !== false){
        break;
    }
    break;
    if($attempts >= 9){
        throw new \RuntimeException('Failed to connect to ' . $this->getUri() . ' after ' . $attempts . ' attempts');
    }
    time_sleep_until(microtime(true) + 0.1);
}
        var_dump([
                 "options[timeout_connect]" => $this->options['timeout_connect'],
                 "default_socket_timeout" => ini_get("default_socket_timeout"),
                 "getUri"=> $this->getUri(),
                 "errno" => $errno,
                 "errstr" => $errstr,
                 "getStreamContext" => $this->getStreamContext(),
                 "stream_get_transports" => \stream_get_transports(),
                 "/tmp/stream_socket_server.txt" => file_get_contents(sys_get_temp_dir()."/stream_socket_server.txt"),
                 "/usr/bin/env php -v 2>&1" => shell_exec("/usr/bin/env php -v 2>&1"),
        ]);
        sleep(10);
        if (!$this->socket) {
            throw new \Wrench\Exception\ConnectionException(\sprintf('Could not connect to socket: %s (%d)', $errstr, $errno));
        }

        \stream_set_timeout($this->socket, $this->options['timeout_socket']);

        return $this->connected = true;
    }

    /**
     * Configure the client socket.
     *
     * Options include:
     *
     *     - ssl_verify_peer       => boolean, whether to perform peer verification
     *                                 of SSL certificate used
     *     - ssl_allow_self_signed => boolean, whether ssl_verify_peer allows
     *                                 self-signed certs
     *     - timeout_connect       => int, seconds, default 2
     *
     * @param array $options
     */
    protected function configure(array $options): void
    {
        $options = \array_merge([
            'timeout_connect' => self::TIMEOUT_CONNECT,
            'ssl_verify_peer' => false,
            'ssl_allow_self_signed' => true,
        ], $options);

        parent::configure($options);
    }

    protected function getSocketStreamContextOptions(): array
    {
        $options = [];

        return $options;
    }

    protected function getSslStreamContextOptions(): array
    {
        $options = [];

        if ($this->options['ssl_verify_peer']) {
            $options['verify_peer'] = true;
        }

        if ($this->options['ssl_allow_self_signed']) {
            $options['allow_self_signed'] = true;
        }

        return $options;
    }
}
