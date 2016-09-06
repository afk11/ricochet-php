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
        $this->channelId = $channelId;
        $this->dataLen = $contentLen;
        $this->data = $content;
    }

    /**
     * @param $id
     * @param $content
     * @return Message
     */
    public static function create($id, $content)
    {
        return new self($id, strlen($content) + 4, $content);
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
        $packet = pack("nn", $this->dataLen, $this->channelId) . $this->data;
        return new Buffer($packet);
    }
}
