<?php

namespace Ricochet\Channel\AuthHiddenService;


use Ricochet\Channel\AuthHiddenService\Proto\Packet;
use Ricochet\Channel\AuthHiddenService\Proto\Proof;
use Ricochet\Channel\AuthHiddenService\Proto\Result;
use Ricochet\Channel\Control\Proto\ChannelResult;
use Ricochet\Channel\Control\Proto\OpenChannel;
use Ricochet\Connection;
use Ricochet\Key\PrivateKey;

class AuthHiddenService
{
    const CHANNEL = 'im.ricochet.auth.hidden-service';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * ControlChannel constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $that = $this;
        $this->connection->on('msg', [$that, 'onMessage']);
    }

    public function onMessage($msg)
    {
        echo "AuthHiddenService saw the message\n";
        echo $msg.PHP_EOl;
    }

    /**

     * @param null|string $client_cookie
     * @return OpenChannel
     */
    public function prepareOpenChannel($client_cookie = null)
    {
        $msg = new OpenChannel();
        $msg->setChannelIdentifier(0);
        $msg->setChannelType(self::CHANNEL);
        if (!is_null($client_cookie)) {
            if (!is_string($client_cookie)) {
                throw new \RuntimeException('Client cookie must be a string');
            }

            $msg->setExtension('client_cookie', $client_cookie);
        }

        return $msg;
    }

    /**
     * @param null|string $client_cookie
     */
    public function openChannel($client_cookie = null)
    {
        $openChannel = $this->prepareOpenChannel($client_cookie);
        $packet = new \Ricochet\Channel\Control\Proto\Packet();
        $packet->setOpenChannel($openChannel);
        $this->connection->send($packet->serialize());
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
        return hash_hmac('sha256', $clientCookie . $serverCookie, $clientHost . $serverHost);
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
        $signature = $privateKey->sign($proofData);

        $proof = new Proof();
        $proof->setPublicKey($privateKey->getPublicKey());
        $proof->setSignature($signature);

        return $proof;
    }

    /**
     * @param string $clientCookie
     * @param string $clientHost
     * @param string $serverCookie
     * @param string $serverHost
     * @param PrivateKey $privateKey
     */
    public function proof($clientCookie, $clientHost, $serverCookie, $serverHost, PrivateKey $privateKey)
    {
        $proof = $this->prepareProof($clientCookie, $clientHost, $serverCookie, $serverHost, $privateKey);
        $packet = new Packet();
        $packet->setProof($proof);
        $this->connection->send($packet->serialize());
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

        $this->connection->send($packet->serialize());
    }
}













