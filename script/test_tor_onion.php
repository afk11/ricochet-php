<?php


require __DIR__ . '/../src/bootstrap.php';

$onion = getenv('RICOCHET_TESTING_IDENTITY');

$loop = React\EventLoop\Factory::create();
$params = new \Ricochet\Params();
$params->setSupportedVersions([1]);

$connector = new \React\SocketClient\TcpConnector($loop);
$proxyUrl = 'socks://127.0.0.1:9050';
$client = new \Clue\React\Socks\Client($proxyUrl, $connector, $loop);

$connectTo = $onion . ".onion";
$connectTo = "google.com";
echo "start - {$connectTo}:80".PHP_EOL;
$conn = $client->create($connectTo, 80);
$conn->then(function () {
    echo 'connected through proxy';
}, function () {
    echo "failed to connect\n";
});

$loop->run();
