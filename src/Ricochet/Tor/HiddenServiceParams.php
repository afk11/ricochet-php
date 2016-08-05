<?php

namespace Ricochet\Tor;


use Ricochet\Key\PrivateKey;

// https://gitweb.torproject.org/torspec.git/tree/control-spec.txt#n1433
class HiddenServiceParams
{
    const PORTMAX = (2 ** 16) - 1;

    /**
     * @var bool
     */
    private $flagDiscardPK = false;

    /**
     * @var bool
     */
    private $flagDetach = false;

    /**
     * @var bool
     */
    private $flagClientAuth = false;

    /**
     * @var array
     */
    private $clientBlobs = [];

    /**
     * required
     * @var string
     */
    private $keyType;

    /**
     * required
     * @var string
     */
    private $keyBlob;

    /**
     * one is required
     * @var array
     */
    private $targets = [];

    /**
     * @var array
     */
    protected static $listNewKeyTypes = [
        'BEST', 'RSA1024'
    ];

    /**
     * @return boolean
     */
    public function hasFlagDiscardPK()
    {
        return $this->flagDiscardPK;
    }

    /**
     * @return boolean
     */
    public function hasFlagDetach()
    {
        return $this->flagDetach;
    }

    /**
     * @return boolean
     */
    public function hasFlagClientAuth()
    {
        return $this->flagClientAuth;
    }

    /**
     * @return string
     */
    public function getKeyType()
    {
        if (null === $this->keyType) {
            throw new \RuntimeException('Key type not set');
        }

        return $this->keyType;
    }

    /**
     * @return string
     */
    public function getKeyBlob()
    {
        if (null === $this->keyType) {
            throw new \RuntimeException('Key blob not set');
        }

        return $this->keyBlob;
    }

    /**
     * @return array
     */
    public function getTargets()
    {
        if (count($this->targets) < 1) {
            throw new \RuntimeException('No targets set');
        }

        return $this->targets;
    }

    /**
     * @param string $newKeyType
     * @return $this
     */
    public function newKey($newKeyType)
    {
        if (!in_array($newKeyType, static::$listNewKeyTypes)) {
            throw new \RuntimeException('Unknown key type for new key');
        }

        $this->keyType = 'NEW';
        $this->keyBlob = $newKeyType;
        return $this;
    }

    /**
     * @param PrivateKey $key
     * @return $this
     */
    public function withKey(PrivateKey $key)
    {
        $this->keyType = 'RSA1024';
        $this->keyBlob = $key->getTorFormatted();
        return $this;
    }

    /**
     * @return $this
     */
    public function discardPrivateKey()
    {
        $this->flagDiscardPK = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function detach()
    {
        $this->flagDetach = true;
        return $this;
    }

    /**
     * @param string $user
     * @param null|string $pass
     * @return $this
     */
    public function basicAuth($user, $pass = null)
    {
        if (!preg_match('/[A-Za-z0-9+-_]/', $user)) {
            throw new \RuntimeException('Malformed client auth user');
        }

        $blob = $user;
        if (is_string($pass)) {
            $blob .= ":" . $pass;
        }

        $this->flagClientAuth = true;
        $this->clientBlobs[] = $blob;
        return $this;
    }

    /**
     * @param int $port
     * @return bool
     */
    private function validatePort($port)
    {
        return is_int($port) && $port > 0 && $port < self::PORTMAX;
    }

    /**
     * @param string $target
     * @return mixed
     */
    private function parseTarget($target)
    {
        if ($this->validatePort($target)) {
            return $target;
        }

        $explode = explode(":", $target);
        $size = count($explode);
        if (1 === $size) {
            $port = $explode[0];
            if (!$this->validatePort($port)) {
                throw new \RuntimeException('Failed to validate target port');
            }
        } else if (2 === $size) {
            list ($ip, $port) = $explode;
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                throw new \RuntimeException('Failed to validate IP');
            }

            if (!$this->validatePort($port)) {
                throw new \RuntimeException('Failed to validate target port');
            }
        } else {
            throw new \RuntimeException('Unparsable target');
        }

        return $target;
    }

    /**
     * @param int $virtualPort
     * @param null|string $target
     * @return $this
     */
    public function target($virtualPort, $target = null)
    {
        if (!$this->validatePort($virtualPort)) {
            throw new \RuntimeException('Argument 1 to target must be an integer');
        }

        if (null !== $target) {
            $target = $this->parseTarget($target);
            $this->targets[] = [$virtualPort, $target];
        } else {
            $this->targets[] = [$virtualPort];
        }

        return $this;
    }

    /**
     * @throws \RuntimeException
     */
    private function isReady()
    {
        if ($this->keyType === null || $this->keyBlob === null) {
            throw new \RuntimeException('Missing key type');
        }

        if (count($this->targets) < 1) {
            throw new \RuntimeException('Must specify at least one target');
        }
    }

    /**
     * @return string
     */
    private function deriveFlags()
    {
        $flags = '';
        if ($this->flagDetach) {
            $flags .= 'Detach';
        }

        if ($this->flagDiscardPK) {
            $flags .= 'DiscardPK';
        }

        if ($this->flagClientAuth) {
            $flags .= 'BasicAuth';
        }

        if ($flags !== '') {
            $flags = "Flags=" . $flags;
        }

        return $flags;
    }

    /**
     * @return string
     */
    private function deriveTargets()
    {
        $targets = [];
        foreach ($this->targets as $target) {
            switch (count($target)) {
                case 1:
                    $targets[] = "Port=" . $target[0];
                    break;
                case 2:
                    $targets[] = "Port=" . $target[0] . "," . $target[1];
                    break;
            }
        }

        $targets = implode(" ", $targets);
        return $targets;
    }

    /**
     * @return string
     */
    private function deriveClientBlobs()
    {
        return implode(" ", array_map(function ($auth) {
            return 'ClientAuth=' . $auth;
        }, $this->clientBlobs));
    }

    /**
     * @return string
     */
    public function generateCommand()
    {
        $this->isReady();

        return implode(" ", array_filter([
            'ADD_ONION',
            $this->keyType . ":" . $this->keyBlob,
            $this->deriveFlags(),
            $this->deriveTargets(),
            $this->deriveClientBlobs()
        ], 'strlen'));
    }
}