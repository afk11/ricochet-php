<?php
require __DIR__ . '/../vendor/autoload.php';
use Clue\React\Socks\Client;
$loop = React\EventLoop\Factory::create();
$connector = new \React\SocketClient\TcpConnector($loop);
$client = new Client('socks5://127.0.0.1:9050', $connector, $loop);
$client->createConnection('blocktrail.com', 80)->then(function (\React\Stream\Stream $stream) {
    echo 'connected';
    $stream->write("GET / HTTP/1.0\r\n\r\n");
}, function (\Exception $e) {
    echo "connection error\n";
    echo "{$e->getMessage()}\n";
});

$loop->run();
