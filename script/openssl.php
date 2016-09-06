<?php

$priv = openssl_pkey_new([
    "private_key_bits" => 1024,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
]);
$details = openssl_pkey_get_details($priv);
//print_r($priv);
print_r($details);