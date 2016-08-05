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

    private $inbound = false;

    private function onData()
    {

    }

    public function init(Stream $stream)
    {
        $this->stream = $stream;
        $this->stream->on('data', function ($data) {
            echo "stream buffer\n";
            $this->buffer .= $data;
            $tmp = $this->buffer;
            $bufferLen = strlen($tmp);

            while ($bufferLen > 2) {
                echo "Something in there1\n";
                list ($size, $channel, $data) = unpack("SSC*", $tmp);
                if (2 + $size === $bufferLen) {
                    echo "Something in there2   \n";
                    $message = new Message($channel, $size, $data);
                    $this->emit('msg', $message);
                } else {
                    return;
                    //throw new \RuntimeException('Malformed packet - length mismatch');
                }

                $tmp = substr($tmp, $bufferLen);
                $bufferLen = strlen($tmp);
            }
        });

        $this->stream->on('close', function () {
            $this->emit('close');
        });
    }

    public function initializeOutbound(Stream $stream, ParamsInterface $params)
    {
        $this->inbound = false;
        $this->init($stream);

        $deferred = new Deferred();
        $awaitVersion = true;
        $this->stream->on('close', function () use (&$awaitVersion, $deferred) {
            echo "CLOSED????????";
            if ($awaitVersion) {
                $awaitVersion = false;
                $deferred->reject('peer disconnected');
            }
        });

        $this->stream->once('data', function ($data) use ($deferred, $params) {
            echo "first data\n";
            try {
                $response = IntroductionResponse::parse($data);
                if ($response->getVersion() === IntroductionResponse::NO_VERSION) {
                    $deferred->reject($this);
                    echo 'f1'.PHP_EOL;
                    return;
                }

                if (!in_array($response->getVersion(), $params->getSupportedVersions())) {
                    echo 'f2'.PHP_EOL;
                    $deferred->reject($this);
                }

                echo 'fyay'.PHP_EOL;
                $deferred->resolve($this);
            } catch (\Exception $e) {
                echo 'fgen'.PHP_EOL;
                $deferred->reject($this);
            }
        });

        $introduction = new Introduction($params->getSupportedVersions());
        $this->send($introduction->getBuffer()->getBinary());

        return $deferred->promise();
    }

    public function send($data)
    {
        echo "DEBUG: send: ".bin2hex($data).PHP_EOL;
        $this->stream->write($data);
    }

}
