<?php

require __DIR__ . '/../src/bootstrap.php';

use Clue\React\Socks\Client;
$loop = React\EventLoop\Factory::create();
$client = new Client('socks://127.0.0.1:9050', $loop);
$client->setResolveLocal(false);
$connector = $client->createConnector();

//$connector->create('lfwmdpwjdoim4m2t.onion', '1337')->then(function ($stream) {
//$connector->create('g5o6oj7ima4p2g7z.onion', '1337')->then(function ($stream) {

// ricochet instance
$connector->create('7powkj5btsckr5ku.onion', '9878')->then(function ($stream) {
    $stream->on('data', function ($data) {
        echo "GOT DATA BACK!\n";
        echo $data;
    });
    $stream->write("GET / HTTP/1.0\r\n\r\n");
}, function ($e) {
    print_r($e->getMessage());
});

$loop->run();
