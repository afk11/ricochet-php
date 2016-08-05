<?php

namespace Ricochet\Protocol\Message;

class IntroductionResponse
{
    const NO_VERSION = 0xff;

    /**
     * @var int
     */
    private $selectedVersion;

    /**
     * ServerIntroductionResponse constructor.
     * @param int $selectedVersion
     */
    public function __construct($selectedVersion)
    {
        $this->selectedVersion = $selectedVersion;
    }

    /**
     * @param string $message
     * @return IntroductionResponse
     */
    public static function parse($message)
    {
        if (strlen($message) !== 1) {
            throw new \RuntimeException('Invalid length');
        }

        $version = ord($message);
        return new self($version);
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->selectedVersion;
    }
}
