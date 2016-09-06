<?php

namespace Ricochet\Channel;


use Ricochet\Channel\Control\Proto\ChannelResult;
use Ricochet\Protocol\Message\Message;

interface ChannelInterface
{
    /**
     * @param Message $message
     * @return void
     */
    public function onMessage(Message $message);

    /**
     * @param ChannelResult $channelResult
     * @return void
     */
    public function onChannelResult(ChannelResult $channelResult);
}