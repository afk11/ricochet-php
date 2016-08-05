<?php

namespace Ricochet;

class Params implements ParamsInterface
{
    /**
     * @var int
     */
    private $socksTimeout = 0;

    /**
     * @var int[]
     */
    private $supportedVersions = [];

    /**
     * @return \int[]
     */
    public function getSupportedVersions()
    {
        if (count($this->supportedVersions) === 0) {
            throw new \RuntimeException('Supported versions cannot be empty');
        }

        return $this->supportedVersions;
    }

    /**
     * @param int[] $versions
     * @return $this
     */
    public function setSupportedVersions(array $versions)
    {
        array_map(function ($version) {
            if (!is_int($version)) {
                throw new \RuntimeException('Version must be an integer');
            }
        }, $versions);

        $this->supportedVersions = $versions;
        return $this;
    }

    /**
     * @return int
     */
    public function getSocksTimeout()
    {
        return $this->socksTimeout;
    }

    /**
     * @param int $socksTimeout
     * @return Params
     */
    public function setSocksTimeout($socksTimeout)
    {
        $this->socksTimeout = $socksTimeout;
        return $this;
    }
}
