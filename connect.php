<?php

require "vendor/autoload.php";
require_once "src/AuthHiddenService-extensions.php";

use Ricochet\Key\PrivateKey;
use Ricochet\Params;
use Ricochet\RicochetClient;

$privateKey = PrivateKey::generate();

$torControl = new TorControl\TorControl([
    'hostname' => '127.0.0.1',
    'port'     => 9051,
    'password' => 'testtesttesttest',
    'authmethod' => 1
]);

$torControl->connect();
$torControl->authenticate();

$torService = new \Ricochet\Tor\Control\TorService($torControl);
$torService->createEphemeralHiddenService(
    (new \Ricochet\Tor\HiddenServiceParams())
        ->withKey($privateKey)
        ->detach()
        ->target(9878)
);

$params = new Params();
$params->setSupportedVersions([1]);

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$socket->on('connection', function ($conn) {
    echo "Received a connection!\n";
});
$socket->listen(9878);

$client = new RicochetClient($loop, $params);

// ricochet instance
$client->connect('uqdsmlf2yevufedu')->then(function (\Ricochet\Connection $conn) use ($socket) {
    $conn->on('close', [$socket, 'shutdown']);
    $conn->on('data', function ($data) {
        echo "GOT DATA BACK!\n";
        var_dump($data);
        echo $data;
    });

    $hsChannel = new \Ricochet\Channel\AuthHiddenService\AuthHiddenService($conn);
    $hsChannel->openChannel(random_bytes(16));
}, function ($e) {
    print_r($e->getMessage());
});

$loop->run();
