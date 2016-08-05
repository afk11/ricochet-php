<?php


namespace Ricochet\Protocol\Message;

use BitWasp\Buffertools\Buffer;

class Introduction
{
    /**
     * @var array|\int[]
     */
    private $versions = [];

    /**
     * ClientIntroduction constructor.
     * @param int[] $versions
     */
    public function __construct(array $versions)
    {
        $this->versions = $versions;
    }

    /**
     * @return array|\int[]
     */
    public function getVersions()
    {
        return $this->versions;
    }

    public function getBuffer()
    {
        $versions = "\x49\x4d";
        $versions .= chr(count($this->versions));
        foreach ($this->versions as $version) {
            $versions .= chr($version);
        }

        return new Buffer($versions);
    }
}
