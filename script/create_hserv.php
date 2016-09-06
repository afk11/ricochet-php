<?php

require __DIR__ . '/../src/bootstrap.php';

function parseKeys(&$results, $string) {
    list ($key, $value) = explode("=", $string);
    $results[$key] = $value;
}

$destPort = 1337;


// Connect to the TOR server using password authentication
$tc = new TorControl\TorControl(
    array(
        'hostname' => '127.0.0.1',
        'port'     => 9051,
        'password' => 'testtesttesttest',
        'authmethod' => 1
    )
);
$tc->connect();
$tc->authenticate();
// Renew identity
$privateKey = 'NEW:BEST';
$res = $tc->executeCommand('ADD_ONION '.$privateKey.' Flags=Detach Port='.$destPort.',127.0.0.1:'.$destPort);

$parsed = [];
print_r($res);
parseKeys($parsed, $res[0]['message']);
parseKeys($parsed, $res[1]['message']);
echo $parsed['ServiceID'] . ".onion\n" . $destPort.PHP_EOL;
// Quit
$tc->quit();