<?php

namespace Ricochet\Key;

use FG\ASN1\Identifier;
use FG\ASN1\Universal\Sequence;
use phpseclib\Crypt\RSA;

class PrivateKey
{
    /**
     * @var resource
     */
    private $private;

    /**
     * @var PublicKey
     */
    private $public;

    /**
     * PrivateKey constructor.
     * @param string $key - file path to key, or PEM formatted key
     * @param string $passphrase
     */
    public function __construct($key, $passphrase = null)
    {
        if (!is_null($passphrase)) {
            if (!is_string($passphrase)) {
                throw new \RuntimeException('Passphrase must be a string');
            }

            $private = openssl_pkey_get_private($key, $passphrase);
        } else {
            $private = openssl_pkey_get_private($key);
        }

        if (false === $private || !is_resource($private)) {
            throw new \RuntimeException('Unable to parse private key');
        }

        $this->private = $private;
    }

    /**
     * @param null|string $passphrase
     * @return PrivateKey
     */
    public static function generate($passphrase = null)
    {
        $private = openssl_pkey_new([
            "private_key_bits" => 1024,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ]);

        $serialized = '';
        if (!is_null($passphrase)) {
            if (!is_string($passphrase)) {
                throw new \RuntimeException('Passphrase must be a string, or null');
            }

            $result = openssl_pkey_export($private, $serialized, $passphrase);
        } else {
            $result = openssl_pkey_export($private, $serialized);
        }

        if (!$result) {
            throw new \RuntimeException('Failed to create private key');
        }

        return new PrivateKey($serialized, $passphrase);
    }



    /**
     * @param string $data
     * @return string
     */
    public function signaaaa($data)
    {
        $serialized = '';
        openssl_pkey_export($this->private, $serialized);
        $rsa = new RSA();
        $rsa->setHash('sha256');
        $rsa->setMGFHash('sha256');
        $rsa->loadKey($serialized);;
        $rsa->setSignatureMode(RSA::SIGNATURE_PKCS1);
        $signature = $rsa->sign($data);
        return $signature;

    }

    /**
     * @param string $data
     * @return string
     */
    public function signData($data)
    {
        echo "signData: data: ".bin2hex($data).PHP_EOL;
        $hash = hash('sha256', $data, true);
        echo "signData: hash: ".bin2hex($hash).PHP_EOL;
        return $this->signSha256($hash);
    }

    /**
     * @param string $data
     * @return string
     */
    public function signSha256($data)
    {
        echo "Called signSha256\n";

        $signature = '';
        if (!openssl_sign($data, $signature, $this->private, OPENSSL_ALGO_SHA256)) {
            throw new \RuntimeException('Failed to sign data');
        }

        return $signature;
    }

    /**
     * @return string
     */
    public function getPemFormatted()
    {
        $serialized = '';
        if (!openssl_pkey_export($this->private, $serialized)) {
            throw new \RuntimeException('Unable to serialize key - this is quite serious');
        }

        return $serialized;
    }

    /**
     * @param string $pem_data
     * @return string
     */
    private function pem2der($pem_data)
    {
        $begin = "KEY-----";
        $end   = "-----END";
        $pem_data = substr($pem_data, strpos($pem_data, $begin)+strlen($begin));
        $pem_data = substr($pem_data, 0, strpos($pem_data, $end));
        $der = base64_decode($pem_data);
        return $der;
    }

    /**
     * @return string
     */
    public function getTorFormatted()
    {
        $pem = $this->getPemFormatted();
        $der = $this->pem2der($pem);

        $parsed = Sequence::fromBinary($der);
        if (count($parsed) != 3) {
            throw new \RuntimeException('Private key sequence not of correct length');
        }

        if (
            $parsed[0]->getIdentifier() != chr(Identifier::INTEGER)
            || $parsed[1]->getIdentifier() != chr(Identifier::SEQUENCE)
            || $parsed[2]->getIdentifier() != chr(Identifier::OCTETSTRING)
        ) {
            throw new \RuntimeException('Private key contains wrong data');
        }

        return base64_encode(pack("H*", $parsed[2]->getContent()));
    }

    /**
     * @return PublicKey
     */
    public function getPublicKey()
    {
        if (null === $this->public) {
            $details = openssl_pkey_get_details($this->private);
            $this->public = new PublicKey($details['key']);
        }

        return $this->public;
    }
}