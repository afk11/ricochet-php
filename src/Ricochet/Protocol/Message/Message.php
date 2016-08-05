<?php

namespace Ricochet\Protocol\Message;

use BitWasp\Buffertools\Buffer;

class Message
{
    /**
     * @var int
     */
    private $channelId;

    /**
     * @var string
     */
    private $data;

    /**
     * @var int
     */
    private $dataLen;

    /**
     * Packet constructor.
     * @param int $channelId
     * @param int $contentLen
     * @param string $content
     */
    public function __construct($channelId, $contentLen, $content)
    {
        echo "Received message!\n";
        $this->channelId = $channelId;
        $this->dataLen = $contentLen;
        $this->data = $content;
    }

    /**
     * @return int
     */
    public function getChannelId()
    {
        return $this->channelId;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getDataLen()
    {
        return $this->dataLen;
    }

    /**
     * @return Buffer
     */
    public function getBuffer()
    {
        if ($this->dataLen != strlen($this->data)) {
            throw new \RuntimeException('Data length mismatch - unable to serialize');
        }

        $packet = pack("nn", $this->dataLen, $this->channelId) . $this->data;
        return new Buffer($packet);
    }
}
