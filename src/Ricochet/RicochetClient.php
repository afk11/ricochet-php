<?php

namespace Ricochet;

use Clue\React\Socks\Client;
use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;
use React\SocketClient\TcpConnector;
use React\Stream\Stream;
use Ricochet\Protocol\Message\Introduction;

class RicochetClient
{
    const DEFAULT_PORT = 9878;

    /**
     * @var ParamsInterface
     */
    private $params;

    /**
     * @var Client
     */
    private $connector;

    /**
     * RicochetClient constructor.
     * @param LoopInterface $loop
     * @param string $proxyUrl
     */
    public function __construct(LoopInterface $loop, ParamsInterface $params, $proxyUrl = '127.0.0.1:9050')
    {
        $this->params = $params;
        $this->connector = new Client($proxyUrl, new TcpConnector($loop), $loop);
    }

    /**
     * @param string $ricochetId
     * @return mixed
     */
    public function connect($ricochetId)
    {
        $url = $ricochetId . ".onion";
        $port = self::DEFAULT_PORT;
        /** @var PromiseInterface $promise */
        $promise = $this->connector->create($url, $port);
        return $promise->then(function (Stream $stream) {
            $connection = new Connection();
            return $connection->initializeOutbound($stream, $this->params);
        });
    }
}
