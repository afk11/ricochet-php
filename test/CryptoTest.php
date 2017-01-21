<?php

namespace Ricochet\Test;


use Ricochet\Key\PrivateKey;
use Ricochet\Key\PublicKey;

class CryptoTest extends \PHPUnit_Framework_TestCase
{
    protected $alicePriv = "-----BEGIN RSA PRIVATE KEY-----
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

    protected $aliceDigest = "623a1ffc94d8f8edcd5e47fbd45e08deb911d1bc";
    protected $aliceTorId = "mi5b77eu3d4o3tk6";
    protected $aliceSignedData = "23fdcd5c7d40b44a7e49619d9048c81931166a0adb80c8981cc8f9a9e02c3923d5fba6d92ea03dc672d009a5fe1be2b582fb935076f880d9aa55511c33620d2aa23336b579dd7ccd1dbf4c845e4100a114d8ac20dd47229e876444f79d5152456a8e26fefa67a12436b3c33728a2ff7cb12250c486f786647574e48bb9208f64";

    protected $bobPub = "-----BEGIN RSA PUBLIC KEY-----
MIGJAoGBAMP8GyAg/kzwXizpUWjWIMw/lvDffXjsxcq1qmZWZxXJQH/oE8bX+WAf
VS8iUHVqTykubR0W3QNL6aWSZKBqDQUTN0QBJUF4qdkg3x56C0kwcWa+seDMAvJw
pcHK9wN7mtWHIhFwhikP//NylrY1MaUxcPjvOKcdJ90k988nnmpZAgMBAAE=
-----END RSA PUBLIC KEY-----
";

    protected $bobDigest = "b4780cabdfc3593004431644977cf73bf8475848";
    protected $bobTorId = "wr4azk67ynmtabcd";

    public function testAliceBob()
    {
        $data = "test data";

        $aliceKey = new PrivateKey($this->alicePriv);
        $signature = $aliceKey->signSHA256($data);
        $this->assertEquals(hex2bin($this->aliceSignedData), $signature);

        $this->assertTrue($aliceKey->getPublicKey()->verifySha256($data, $signature));

        $signatureSign256 = $aliceKey->signSha256($data);
        $this->assertEquals(hex2bin($this->aliceSignedData), $signatureSign256);
    }

    public function testSign()
    {
        $key = new PrivateKey($this->alicePriv);

        $data = "test data";
        $data2 = "different";
        $publicKey = $key->getPublicKey();

        // Good signature
        $signature = $key->signData($data);
        $this->assertTrue($publicKey->verifyData($data, $signature));

        // Bad signature
        $this->assertFalse($publicKey->verifyData($data2, $signature));

        // Corrupt signature
        $this->assertFalse($publicKey->verifyData($data, substr($signature, -10)));

        // Wrong public key
        $badPublicKey = new PublicKey($this->bobPub);
        $this->assertFalse($badPublicKey->verifyData($data, $signature));

        // Compare to SHA256
        $dataDigest = hash('sha256', $data, true);
        $signature2 = $key->signSHA256($dataDigest);

        $this->assertTrue($publicKey->verifySHA256($dataDigest, $signature2));
        $this->assertTrue($publicKey->verifyData($data, $signature2));
        $this->assertTrue($publicKey->verifySHA256($dataDigest, $signature));

        $signaturep = hex2bin($this->aliceSignedData);
        $this->assertTrue($publicKey->verifySHA256($data, $signaturep));
    }
}