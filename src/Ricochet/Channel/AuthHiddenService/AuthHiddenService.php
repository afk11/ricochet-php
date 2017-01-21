<?php

namespace Ricochet\Channel\AuthHiddenService;


use Evenement\EventEmitter;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use Ricochet\Channel\AuthHiddenService\Proto\Packet;
use Ricochet\Channel\AuthHiddenService\Proto\Proof;
use Ricochet\Channel\AuthHiddenService\Proto\Result;
use Ricochet\Channel\ChannelInterface;
use Ricochet\Channel\Control\Proto\ChannelResult;
use Ricochet\Channel\Control\Proto\OpenChannel;
use Ricochet\Connection;
use Ricochet\Key\PrivateKey;
use Ricochet\Protocol\Message\Message;

class AuthHiddenService extends EventEmitter implements ChannelInterface
{
    const CHANNEL = 'im.ricochet.auth.hidden-service';

    /**
     * @var int
     */
    private $channelId;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Deferred
     */
    private $deferredOpenChannel;

    /**
     * @var Deferred
     */
    private $deferredProof;

    /**
     * AuthHiddenService constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $that = $this;
        $this->on('proof', [$that, 'onProof']);
        $this->on('result', [$that, 'onResult']);
        $this->connection = $connection;
    }

    /**
     * @param int $id
     * @param null|string $client_cookie
     * @return OpenChannel
     */
    public function prepareOpenChannel($id, $client_cookie = null)
    {
        $msg = new OpenChannel();
        $msg->setChannelIdentifier($id);
        $msg->setChannelType(self::CHANNEL);
        if (!is_null($client_cookie)) {
            if (!is_string($client_cookie)) {
                throw new \RuntimeException('Client cookie must be a string');
            }

            $msg->setExtension('Ricochet\Channel\AuthHiddenService\Proto\client_cookie', $client_cookie);
        }

        return $msg;
    }

    /**
     * @param int $id
     * @param string $client_cookie
     * @return \React\Promise\Promise|PromiseInterface
     */
    public function openChannel($id, $client_cookie)
    {
        if ($this->deferredOpenChannel !== null) {
            throw new \RuntimeException('Pending channel open for AuthHiddenService');
        }

        if (strlen($client_cookie) !== 16) {
            throw new \RuntimeException('Client cookie must be 16 bytes');
        }

        $deferred = new Deferred();
        $openChannel = $this->prepareOpenChannel($id, $client_cookie);
        $packet = new \Ricochet\Channel\Control\Proto\Packet();
        $packet->setOpenChannel($openChannel);
        $message = Message::create(0, $packet->serialize());
        $this->connection->send($message->getBuffer()->getBinary());

        $this->deferredOpenChannel = $deferred;
        return $deferred->promise();
    }

    /**
     * @param int $id
     * @param bool $resultOpened
     * @param null|int $errorCode
     * @param null|string $errorMessage
     * @param null|string $server_cookie
     * @return ChannelResult
     */
    public function prepareChannelResult($id, $resultOpened, $errorCode = null, $errorMessage = null, $server_cookie = null)
    {
        $msg = new ChannelResult();
        $msg->setChannelIdentifier($id);
        $msg->setOpened($resultOpened);

        if (!is_null($errorCode)) {
            if (!in_array($errorCode, [
                ChannelResult\CommonError::GenericError,
                ChannelResult\CommonError::UnknownTypeError,
                ChannelResult\CommonError::UnauthorizedError,
                ChannelResult\CommonError::BadUsageError,
                ChannelResult\CommonError::FailedError,
            ])) {
                throw new \RuntimeException('Invalid error code');
            }

            $msg->setCommonError($errorCode);
            if (is_string($errorMessage)) {
                $msg->setErrorMessage($errorCode);
            }
        }

        if (!is_null($server_cookie)) {
            if (!is_string($server_cookie)) {
                throw new \RuntimeException('Client cookie must be a string');
            }

            $msg->setExtension('client_cookie', $server_cookie);
        }

        return $msg;
    }

    /**
     * @param int $id
     * @param bool $resultOpened
     * @param null|int $errorCode
     * @param null|string $errorMessage
     * @param null|string $server_cookie
     */
    public function channelResult($id, $resultOpened, $errorCode = null, $errorMessage = null, $server_cookie = null)
    {
        $openChannel = $this->prepareChannelResult($id, $resultOpened, $errorCode, $errorMessage, $server_cookie);
        $packet = new \Ricochet\Channel\Control\Proto\Packet();
        $packet->setChannelResult($openChannel);
        $this->connection->send($packet->serialize());
    }

    /**
     * @param string $clientCookie
     * @param string $clientHost
     * @param string $serverCookie
     * @param string $serverHost
     * @return string
     */
    public function generateProofString($clientCookie, $clientHost, $serverCookie, $serverHost)
    {
        echo "proof: " . $clientHost. $serverHost.PHP_EOL;
        echo "salt: " . $clientCookie . $serverCookie.PHP_EOL;
        return hash_hmac('sha256', $clientHost . $serverHost, $clientCookie . $serverCookie, true);
    }

    /**
     * @param string $clientCookie
     * @param string $clientHost
     * @param string $serverCookie
     * @param string $serverHost
     * @param PrivateKey $privateKey
     * @return Proof
     */
    public function prepareProof($clientCookie, $clientHost, $serverCookie, $serverHost, PrivateKey $privateKey)
    {
        $proofData = $this->generateProofString($clientCookie, $clientHost, $serverCookie, $serverHost);
        $signature = $privateKey->signSha256($proofData);
        if (!$privateKey->getPublicKey()->verifySha256($proofData, $signature)) {
            throw new \RuntimeException('Exceptional circumstances');
        }

        $proof = new Proof();
        $proof->setPublicKey($privateKey->getPublicKey()->getDerFormatted());
        $proof->setSignature($signature);

        return $proof;
    }

    /**
     * @param string $channelId
     * @param string $clientCookie
     * @param string $clientHost
     * @param string $serverCookie
     * @param string $serverHost
     * @param PrivateKey $privateKey
     * @return PromiseInterface
     */
    public function proof($channelId, $clientCookie, $clientHost, $serverCookie, $serverHost, PrivateKey $privateKey)
    {
        if ($this->deferredProof instanceof Deferred) {
            throw new \RuntimeException('Already awaiting Proof message');
        }

        try {
            $deferred = $this->deferredProof = new Deferred();
            $proof = $this->prepareProof($clientCookie, $clientHost, $serverCookie, $serverHost, $privateKey);
            $packet = new Packet();
            $packet->setProof($proof);
            $message = Message::create($channelId, $packet->serialize());
            $this->connection->send($message->getBuffer()->getBinary());

            return $deferred->promise();
        } catch (\Exception $e) {
            echo "Failure: \n".$e->getMessage().PHP_EOL;
        }

    }

    /**
     * @param bool $accepted
     * @param bool $isKnown
     * @return Result
     */
    public function prepareResult($accepted, $isKnown)
    {
        $result = new Result();
        $result->setAccepted($accepted);
        $result->setIsKnownContact($isKnown);

        return $result;
    }

    /**
     * @param bool $accepted
     * @param bool $isKnown
     */
    public function result($accepted, $isKnown)
    {
        $result = $this->prepareResult($accepted, $isKnown);
        $packet = new Packet();
        $packet->setResult($result);
        $message = Message::create($this->channelId, $packet->serialize());
        $this->connection->send($message->getBuffer()->getBinary());
    }

    public function onProof(Proof $proof)
    {

    }

    /**
     * @param Result $result
     */
    public function onResult(Result $result)
    {
        if ($this->deferredProof instanceof Deferred) {
            $deferred = $this->deferredProof;
            $deferred->resolve($result);
            $this->deferredProof = null;
        } else {
            // why would we receive a Result if we weren't authenticating?
        }
    }

    /**
     * @param $id
     * @param PrivateKey $privateKey
     * @param $clientHost
     * @param $serverHost
     * @return PromiseInterface
     */
    public function authenticate($id, PrivateKey $privateKey, $clientHost, $serverHost)
    {
        $clientCookie = 'DEFADEFADEFADEFA';
        $sendProof = function ($serverCookie) use ($id, $clientCookie, $clientHost, $serverHost, $privateKey) {
            echo 'Preparing proofHMAC' . PHP_EOL;
            return $this->proof($id, $clientCookie, $clientHost, $serverCookie, $serverHost, $privateKey);
        };

        return $this->openChannel($id, $clientCookie)->then(function (ChannelResult $result) use ($sendProof, $privateKey) {
            $serverCookie = $result->getExtension('Ricochet\Channel\AuthHiddenService\Proto\server_cookie');
            return $sendProof($serverCookie);
        }, function (\Exception $err) {
            echo $err->getMessage().PHP_EOL;
        });
    }

    /**
     * @param Message $msg
     */
    public function onMessage(Message $msg)
    {
        $packet = new Proto\Packet($msg->getData());
        if ($packet->hasProof()) {
            $this->emit('proof', [$packet->getProof()]);
        } else if ($packet->hasResult()) {
            $this->emit('result', [$packet->getResult()]);
        }
    }

    public function onChannelResult(ChannelResult $channelResult)
    {
        if ($this->deferredOpenChannel instanceof Deferred) {
            $deferred = $this->deferredOpenChannel;
            $deferred->resolve($channelResult);
            $this->deferredOpenChannel = null;
        } else {
            // strange..
        }
    }

}