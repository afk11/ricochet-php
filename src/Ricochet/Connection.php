<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8/3/16
 * Time: 10:39 PM
 */

namespace Ricochet;

use Evenement\EventEmitter;
use React\Promise\Deferred;
use React\Stream\Stream;
use Ricochet\Channel\ChannelInterface;
use Ricochet\Channel\Control\ControlChannel;
use Ricochet\Channel\Control\Proto\Packet;
use Ricochet\Protocol\Message\Introduction;
use Ricochet\Protocol\Message\IntroductionResponse;
use Ricochet\Protocol\Message\Message;

class Connection extends EventEmitter
{
    /**
     * @var Stream
     */
    private $stream;

    /**
     * @var string
     */
    private $buffer;
    private $handshake = false;
    private $inbound = false;

    /**
     * @var ChannelInterface
     */
    private $channels = [];

    public function init(Stream $stream)
    {
        $this->stream = $stream;
        $this->stream->on('data', function ($data) {
            echo "data: ".bin2hex($data).PHP_EOL;
            if ($this->handshake) {
                $this->buffer .= $data;
                $tmp = $this->buffer;
                $bufferLen = strlen($tmp);

                while ($bufferLen > 2) {
                    $pkt = unpack('nsize/nchannel', $tmp);
                    $pkt['data1'] = substr($tmp, 4, $pkt['size']);

                    $message = new Message($pkt['channel'], $pkt['size'], $pkt['data1']);
                    $this->emit('msg', [$message]);
                    if (isset($this->channels[$message->getChannelId()])) {
                        $this->channels[$message->getChannelId()]->onMessage($message);
                    }

                    $tmp = substr($tmp, 4 + $pkt['size']);
                    $bufferLen = strlen($tmp);
                }
            }
        });

        $this->stream->on('close', function () {
            $this->emit('close');
        });

        $this->on('channel.result', function (Message $message) {
            if (isset($this->channels[$message->getChannelId()])) {
                $this->channels[$message->getChannelId()]->onMessage($message);
            };
        });
    }

    /**
     * @param int $id
     * @return ChannelInterface
     */
    public function channel($id)
    {
        echo "looking for $id in " . implode(" ", array_keys($this->channels)).PHP_EOL;
        if (!isset($this->channels[$id])) {
            throw new \RuntimeException('Channel not found');
        }

        return $this->channels[$id];
    }

    public function addNewChannel($id, ChannelInterface $channel)
    {
        if (isset($this->channels[$id])) {
            throw new \RuntimeException('Channel ID already in use');
        }

        $this->channels[$id] = $channel;
        $this->emit('channel.new', [$id, $channel]);
    }

    public function initializeOutbound(Stream $stream, ParamsInterface $params)
    {
        $this->inbound = false;
        $this->init($stream);

        $deferred = new Deferred();
        $awaitVersion = true;
        $this->stream->on('close', function () use (&$awaitVersion, $deferred) {
            if ($awaitVersion) {
                $awaitVersion = false;
                $deferred->reject('peer disconnected');
            }
        });

        $this->stream->once('data', function ($data) use ($deferred, $params) {
            try {
                $response = IntroductionResponse::parse($data);
                if ($response->getVersion() === IntroductionResponse::NO_VERSION) {
                    echo "response reject no version\n";
                    $deferred->reject($this);
                    return;
                }

                if (!in_array($response->getVersion(), $params->getSupportedVersions())) {
                    echo "unsupported version\n";
                    $deferred->reject($this);
                }

                echo "setting up channel \n";
                $this->handshake = true;
                $this->addNewChannel(0, new ControlChannel($this));
                $deferred->resolve($this);
            } catch (\Exception $e) {
                echo "introduction exception: " . $e->getMessage().PHP_EOL;
                $deferred->reject($this);
            }
        });

        $introduction = new Introduction($params->getSupportedVersions());
        $this->send($introduction->getBuffer()->getBinary());

        return $deferred->promise();
    }

    /**
     * @param $data
     */
    public function send($data)
    {
        echo "DEBUG: send: ".bin2hex($data).PHP_EOL;
        $this->stream->write($data);
    }

}
