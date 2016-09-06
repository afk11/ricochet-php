<?php

require __DIR__ . '/../src/bootstrap.php';

$serialized = '-----BEGIN PRIVATE KEY-----
MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAN3VY48WRjup96fB
TisT3+Uw1OnbmdvB1rGrAObFK9JT8TUXAlz0V54uCF0TedH/dtTd6L010KNgD2fV
2jBHLXzOym51q3lrkpHxSTOqp5pVM6U1y2KJ/QhnTIGGpk/h30LWbINXYaPRxMD6
dV4X+2uVDSLRHbjeRAn0a5eqmXY5AgMBAAECgYEAhnJWLNqrJm4VEy8tWR5qjFXU
NQhLb81DedrSaQsHTCpj/nE7lWrhz5TGrOKo6oWSV+FGtaZwFRSbQaty2d/JyMIO
ReGysJiQ0qkGH2Yps6olurUHm3F/jIiRCLN0b6oQK5irP6VOo8kfdzV2zXIDY7Fj
Jfb034Tvqp+5CeWqtoECQQDvYZApCBH5DNKiesVn4zFsFG9VVyxvzBnbkvsW+G/G
FpZvWH5g+Z8WXWX27nexiCd3whtA8Q1MHHUHAHPzTdplAkEA7Tv1jbRaVHQKhfi1
O4qmWLDWuqUWija4JfWIrQ4enCvxfSdaStKWfw52XGEWS4ey8Iuw7WDcE/2CyCr8
voklRQJAPVw11r6x1LQbvfhYZ6POBFVMoISC6HlZ23XWlPHDvPQHRa1aX8M8qz/v
phdEaSZsb386+y+O6AaXXN8Z2bEIHQJAUnMJR4OL9VgTJDao/hWU9LQZHOstZ0HX
RFIOe16x4sMe/clEh0ajSWtEVZzke8Ggvhs+lXGZa1UrM9hE2Q+fJQJBAOpw53SQ
61V6CuW7BuTCke8J3p2aUTTdYEQUdKViEyMMtEOQmSlf2Nhr74ony1vs2Bg9+D8P
r0oT3u5/c4yPPs4=
-----END PRIVATE KEY-----';

$private = new \Ricochet\Key\PrivateKey($serialized);
$public = $private->getPublicKey();
$onion = $public->getOnion();
$destPort = 1337;

echo $onion.PHP_EOL;

function parseKeys(&$results, $string) {
    list ($key, $value) = explode("=", $string);
    $results[$key] = $value;
}

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
//$privateKey = 'RSA1024:' . $private->getTorFormatted();
$privateKey = 'NEW:BEST';
$res = $tc->executeCommand('ADD_ONION '.$privateKey.' Flags=Detach Port='.$destPort.',127.0.0.1:'.$destPort);

$parsed = [];
print_r($res);
parseKeys($parsed, $res[0]['message']);
parseKeys($parsed, $res[1]['message']);
echo $parsed['ServiceID'] . ".onion\n" . $destPort.PHP_EOL;
// Quit
$tc->quit();