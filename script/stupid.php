<?php

require __DIR__ . '/../src/bootstrap.php';

use Clue\React\Socks\Client;
$loop = React\EventLoop\Factory::create();
$client = new Client('socks://127.0.0.1:9050', $loop);
$client->setResolveLocal(false);
$connector = $client->createConnector();

$connector->create('www.google.com', '80')->then(function ($stream) {
    $stream->on('data', function ($data) {
        echo "GOT DATA BACK!\n";
        echo $data;
        die();
    });
    $stream->write("GET / HTTP/1.0\r\n\r\n");
}, function ($e) {
    print_r($e->getMessage());
});

$loop->run();