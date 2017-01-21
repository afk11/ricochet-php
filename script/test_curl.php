<?php

$proxy = 'localhost:9050';
$url = 'http://google.com/';
//$proxyauth = 'user:password';

$ch = curl_init();
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_PROXYTYPE, 7);
//curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_URL, $url);
$curl_scraped_page = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error)
    echo $error;
elseif ($curl_scraped_page)
    echo $curl_scraped_page;
