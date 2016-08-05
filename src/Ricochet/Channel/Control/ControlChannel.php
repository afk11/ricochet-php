<?php

namespace Ricochet\Channel\Control;

use Ricochet\Channel\Control\Proto\ChannelResult;
use Ricochet\Channel\Control\Proto\EnableFeatures;
use Ricochet\Channel\Control\Proto\KeepAlive;
use Ricochet\Channel\Control\Proto\OpenChannel;
use Ricochet\Channel\Control\Proto\Packet;
use Ricochet\Connection;
use Ricochet\Protocol\Message\Message;

class ControlChannel
{

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
        $this->connection->on('msg', [$this, 'onMessage']);
    }

    /**
     * @param int $id
     * @param string $type
     * @return OpenChannel
     */
    public function prepareOpenChannel($id, $type)
    {
        $msg = new OpenChannel();
        $msg->setChannelIdentifier($id);
        $msg->setChannelType($type);

        return $msg;
    }

    /**
     * @param int $id
     * @param string $type
     */
    public function openChannel($id, $type)
    {
        $openChannel = $this->prepareOpenChannel($id, $type);
        $packet = new Packet();
        $packet->setOpenChannel($openChannel);
        $this->connection->send($packet->serialize());
    }

    /**
     * @param int $id
     * @param bool $resultOpened
     * @param null|int $errorCode
     * @param null|string $errorMessage
     * @return ChannelResult
     */
    public function prepareChannelResult($id, $resultOpened, $errorCode = null, $errorMessage = null)
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

        return $msg;
    }

    /**
     * @param int $id
     * @param bool $resultOpened
     * @param null|int $errorCode
     * @param null|string $errorMessage
     */
    public function channelResult($id, $resultOpened, $errorCode = null, $errorMessage = null)
    {
        $channelResult = $this->prepareChannelResult($id, $resultOpened, $errorCode, $errorMessage);
        $packet = new Packet();
        $packet->setChannelResult($channelResult);
        $this->connection->send($packet->serialize());
    }

    /**
     * @param array $features
     * @return EnableFeatures
     */
    public function prepareEnableFeatures(array $features)
    {
        $msg = new EnableFeatures();
        foreach ($features as $feature) {
            $msg->addFeature($feature);
        }

        return $msg;
    }

    /**
     * @param array $features
     */
    public function enableFeatures(array $features)
    {
        $enableFeatures = $this->prepareEnableFeatures($features);
        $packet = new Packet();
        $packet->setEnableFeatures($enableFeatures);
        $this->connection->send($packet->serialize());
    }

    /**
     * @param null|bool $responseRequested
     * @return KeepAlive
     */
    public function prepareKeepAlive($responseRequested = null)
    {
        $msg = new KeepAlive();
        if (!is_null($responseRequested)) {
            if (!is_bool($responseRequested)) {
                throw new \RuntimeException('KeepAlive response_requested field must be a boolean');
            }

            $msg->setResponseRequested($responseRequested);
        }

        return $msg;
    }

    /**
     * @param null|bool $responseRequested
     */
    public function keepAlive($responseRequested = null)
    {
        $keepAlive = $this->prepareKeepAlive($responseRequested);
        $packet = new Packet();
        $packet->setKeepAlive($keepAlive);
        $this->connection->send($packet->serialize());
    }

    /**
     * @param Message $message
     * @return bool
     */
    public function onMessage(Message $message)
    {
        try {
            $parsed = new Proto\Packet();
            $parsed->parse($message->getData());


            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
