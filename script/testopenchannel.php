<?php

require __DIR__ . '/../src/bootstrap.php';

$connection= new \Ricochet\Connection();
$Auth = new \Ricochet\Channel\AuthHiddenService\AuthHiddenService($connection);

$openChannel = $Auth->prepareOpenChannel(random_bytes(16));
echo "Open channel: \n\n";
echo bin2hex($openChannel->serialize()) . PHP_EOL. PHP_EOL;
echo $openChannel->serialize() . PHP_EOL. PHP_EOL;

/*
$packet = new \Ricochet\Channel\Control\Proto\Packet();
$packet->setOpenChannel($openChannel);
echo bin2hex($packet->serialize()) . PHP_EOL;
echo $packet->serialize() . PHP_EOL;*/