<?php

require __DIR__ . '/../src/bootstrap.php';

use Ricochet\Key\PrivateKey;
use Ricochet\Params;
use Ricochet\RicochetClient;
use Ricochet\Channel\AuthHiddenService\Proto\Result;

$privateKey = new PrivateKey('-----BEGIN PRIVATE KEY-----
MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAMWSPesEAAjLAjFp
KFPiRIE0u04iLt0bIp3bw0xyTb0zuhYviSjY762U68IIG4/s6kPEKGuqe2rfK3vD
PEp8824/tTx9895frNs4I0nb9Nk1s2PP8Rm3uedMGn4hAZMXAvxY5aPI9gDr5QDI
URDTKRM8RpBGn1gr9vkEPQJw8tOHAgMBAAECgYARmXtmig6uudbSK/nprwhHMjlV
NnpSO+6TfVYiYzRFnGwBOe7P8rM3FUMDH9HEumgL7Vdkb+VamdK3zaZ7RDIzAcDD
R+OrO26Y1vyAa1eBX9w+k0EMFra8mKzsONEHYMIPJjjFmZhBsB+3mFD9qihWNpHH
lpPoHZLTX32/OBT3wQJBAPai4Jp1mgemDMDvaPlfqFPK8AWPlxAtSnmx3x7DulKe
75dns9Todip2olRgN3cr0fDRZ9hM3aWtPAt2ZXBm57cCQQDNEn0vwG6nwSjnZsVx
A0/03kKGQOoQlEXZGFXKJUgoODQInwIt2rTsV9eRHqFEkq6H4lbPKqG/9EaPqR1R
DFKxAkAIDbF/2a856LYp5qdq3TDF666Cv/mS0afI6YH7ozCGWiJAs2Yv4ZdaM52B
W9Lz1T55upzFd10Vd96qESemz/VpAkAI/9K2kb9JZVSiMwRfHUIZANfyhE7BQ4B9
MnAxWsl72luONUwnLv3ZkVFIcQuqsrUuCWS92qUWg2XFUCqVL/FBAkEA0v7qMq7f
oWmjVu7HYpVUCPI87t9fZzo/GZ+AAKPhFLpLEbnDpvS8yau0bVxM9Zk/p9/BTGUM
coLqkASQC28sJg==
-----END PRIVATE KEY-----
');
$remoteId = 'bvs43bfavogohkqz';

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
$client->connect($remoteId)->then(function (\Ricochet\Connection $conn) use ($socket, $privateKey, $remoteId) {
    $conn->on('close', [$socket, 'shutdown']);

    $hsId = 1;
    $hsChannel = new \Ricochet\Channel\AuthHiddenService\AuthHiddenService($conn);
    $conn->addNewChannel($hsId, $hsChannel);
    $hsChannel->authenticate($hsId, $privateKey, $privateKey->getPublicKey()->getOnion(), $remoteId)->then(function (Result $result) {
        echo "got the result\n";
        echo $result->getAccepted() ? 'yes - accepted' : 'not accepted';
        echo PHP_EOL;
    });
}, function ($e) {
    print_r($e->getMessage());
});

$loop->run();
