<?php

require __DIR__ . '/../src/bootstrap.php';

$loop = React\EventLoop\Factory::create();
$params = new \Ricochet\Params();
$params->setSupportedVersions([1]);
$client = new \Ricochet\RicochetClient($loop, $params);

// ricochet instance
$client->connect('7powkj5btsckr5ku')->then(function (\Ricochet\Connection $stream) {
    $stream->on('data', function ($data) {
        echo "GOT DATA BACK!\n";
        var_dump($data);
        echo $data;
    });

}, function ($e) {
    print_r($e->getMessage());
});

$loop->run();
