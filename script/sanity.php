<?php

require __DIR__ . '/../src/bootstrap.php';

$aliceStr = "-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQDAS9nLWyK0jWZ8yduqVEhSyZRplTaeUpGWYRi14n1C4sjO6nqm
ES31UCGDH4nIor2R/XMJCJkJwK+t2XrtiH+jUEHwUGhnMkm3hW5NHt5g39s9YK7l
xD39O8N2tHUycVq8guhrb1WBQ2/bmZ85nOIuBDZxIuVQZA1U1L6rWGvm+wIDAQAB
AoGAewYL6JX9thVgpCVga7BQNObSFFpp/xBEJDkqXfLwwIHmhrpsjSIgjPke94yN
0daMAYJsvjLJ9ftYaZjhlGXngbBJiAU95gcZoTAsn2hNJP22ndGuhi6WEKhYwRxK
U5d+3Khzy/ysuoay7DSVtpSmpiacWPSiiptEkxNbcbGba8ECQQDeEGoPASmxZoh4
I2JNQkqSwMKsOZpp/SJhnmLCPoA1oDwlGtu4HF7t9hBXeyIXgLvbfJudFEa+LqR7
wrKQPn0fAkEA3a7cR7eSRNu1ak7gVfQfnP4tFl3+7UC2hUqVHLA5ks4pLl7/ITa+
3P04SOs3WpvZJHYJ+hi/anqEPYrD/3B+pQJBAKmjnnHh8IjODDjCxyjAGJntWYoZ
4yVOtEIgrc830delley+jNUkDzz3+dnqfcu4k0oD8hjYUYaduRe2T5Szt/8CQQDC
EVt8WUNujp0R9P1FohKu4IFeLGmJD/b5V2KUm927HEpG8xkM3Z1XX0KP64MpCnid
B80SKeog8CKmsb2F+NiVAkBT1CEAdiFYtf72hnZCLBw5HrqpN+zjw00GjtlrmmNV
+ILb/YRp5flCY5Se95ExzQqRKzvK5iJg0yEOVF0OcbO+
-----END RSA PRIVATE KEY-----";

$data = 'test data';

$private = new \Ricochet\Key\PrivateKey($aliceStr);
$public = $private->getPublicKey();

echo " [ PHPSECLIB ] \n";
$sss = new \phpseclib\Crypt\RSA();
$sss->setPrivateKey($aliceStr);
$sss->setSignatureMode(\phpseclib\Crypt\RSA::SIGNATURE_PKCS1);
$sss->setHash('sha256');
$s = $sss->sign($data);
echo bin2hex($s).PHP_EOL.PHP_EOL;

echo " [ OPENSSL - sign256 ] \n";
$sig256 = $private->signSha256($data);
echo "Data       256 : Signature: ".bin2hex($sig256).PHP_EOL;
$result = $public->verifySha256($data, $sig256);
echo PHP_EOL;


echo " [ OPENSSL - signData ] \n";
$signature = $private->signData($data);
echo "Hash(Data) 256 : Signature: ".bin2hex($signature).PHP_EOL;
$result = $public->verifyData($data, $signature);
echo PHP_EOL.PHP_EOL;


