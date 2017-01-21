<?php

require __DIR__ . '/../src/bootstrap.php';

use Ricochet\Key\PrivateKey;

$privateKey = PrivateKey::generate();
$remoteId = 'uqdsmlf2yevufedu';

$torControl = new TorControl\TorControl([
    'hostname' => '127.0.0.1',
    'port'     => 9051,
    'password' => getenv('TOR_CONTROL_PASS'),
    'authmethod' => 1
]);

$torControl->connect();
$torControl->authenticate();

$torService = new \Ricochet\Tor\Control\TorService($torControl);
$result = $torService->createEphemeralHiddenService(
    (new \Ricochet\Tor\HiddenServiceParams())
        ->withKey($privateKey)
        ->detach()
        ->target(9878)
);

$torService->deleteEphemeralHiddenService($result['ServiceID']);