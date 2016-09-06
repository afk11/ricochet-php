<?php

require __DIR__ . '/../src/bootstrap.php';

$loop = React\EventLoop\Factory::create();

$tcpConnector = new \React\SocketClient\TcpConnector($loop);

$tcpConnector->create('127.0.0.1', 1337)->then(function (React\Stream\Stream $stream) {
    echo 'connected';
    $stream->on('data', function ($data) {
        echo "got back: $data\n";
    });
    $stream->write('...');

    echo 'wrote';
    $stream->end();
});

$loop->run();