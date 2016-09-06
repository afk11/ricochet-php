<?php

namespace Ricochet\Channel\ContactRequest;


use Ricochet\Channel\ChannelInterface;
use Ricochet\Channel\ContactRequest\Proto\Response;
use Ricochet\Channel\Control\Proto\ChannelResult;
use Ricochet\Channel\Control\Proto\OpenChannel;
use Ricochet\Channel\Control\Proto\Packet;
use Ricochet\Connection;
use Ricochet\Protocol\Message\Message;

class ContactRequest implements ChannelInterface
{
    const CHANNEL = 'im.ricochet.contact.request';

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
    }

    /**
     * @param int $id
     * @param ContactRequest $contactRequest
     * @return OpenChannel
     */
    public function prepareOpenChannel($id, ContactRequest $contactRequest)
    {
        $msg = new OpenChannel();
        $msg->setChannelIdentifier($id);
        $msg->setChannelType(self::CHANNEL);
        $msg->setExtension('contact_request', $contactRequest);

        return $msg;
    }

    /**
     * @param int $id
     * @param bool $resultOpened
     * @param null|int $errorCode
     * @param null|string $errorMessage
     * @param Response $response
     * @return ChannelResult
     */
    public function prepareChannelResult($id, $resultOpened, $errorCode = null, $errorMessage = null, Response $response)
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

        $msg->setExtension('response', $response);

        return $msg;
    }

    /**
     * @param int $id
     * @param bool $resultOpened
     * @param null|int $errorCode
     * @param null|string $errorMessage
     * @param Response $response
     */
    public function channelResult($id, $resultOpened, $errorCode = null, $errorMessage = null, Response $response = null)
    {
        $openChannel = $this->prepareChannelResult($id, $resultOpened, $errorCode, $errorMessage, $response);
        $packet = new \Ricochet\Channel\Control\Proto\Packet();
        $packet->setChannelResult($openChannel);
        $this->connection->send($packet->serialize());
    }

    /**
     * @param null|string $nickname
     * @param null|string $message
     * @return Proto\ContactRequest
     */
    public function prepareContactRequest($nickname = null, $message = null)
    {
        $contactRequest = new Proto\ContactRequest();
        if (!is_null($nickname)) {
            if (!is_string($nickname)) {
                throw new \RuntimeException('Nickname must be a string');
            }
            $contactRequest->setNickname($nickname);
        }
        if (!is_null($message)) {
            if (!is_string($message)) {
                throw new \RuntimeException('Message must be a string');
            }
            $contactRequest->setMessageText($message);
        }

        return $contactRequest;
    }

    /**
     * @param int $id
     * @param null|string $nickname
     * @param null|string $message
     */
    public function contactRequest($id, $nickname = null, $message = null)
    {
        $contactRequest = $this->prepareContactRequest($nickname, $message);
        $openChannel = $this->prepareOpenChannel($id, $contactRequest);
        $packet = new \Ricochet\Channel\Control\Proto\Packet();
        $packet->setOpenChannel($openChannel);
        $this->connection->send($packet->serialize());
    }

    /**
     * @param int $status
     * @param null|string $errorMessage
     * @return Response
     */
    public function prepareResponse($status, $errorMessage = null)
    {
        $response = new Proto\Response();
        $response->setStatus($status);
        if (!is_null($errorMessage)) {
            if (!is_string($errorMessage)) {
                throw new \RuntimeException('Error message must be a string');
            }
            $response->setErrorMessage($errorMessage);
        }

        return $response;
    }

    /**
     * @param OpenChannel $openChannel
     */
    public function onOpenChannel(OpenChannel $openChannel)
    {
        // TODO: Implement onChannelResult() method.
    }

    /**
     * @param ChannelResult $channelResult
     */
    public function onChannelResult(ChannelResult $channelResult)
    {
        // TODO: Implement onChannelResult() method.
    }

    /**
     * @param int $id
     * @param int $status
     * @param int $errorCode
     * @param null|string $errorMessage
     */
    public function response($id, $status, $errorCode, $errorMessage = null)
    {
        $response = $this->prepareResponse($status, $errorMessage);
        $channelResult = $this->prepareChannelResult($id, true, $errorCode, $errorMessage, $response);
        $packet = new Packet();
        $packet->setChannelResult($channelResult);
        $this->connection->send($packet->serialize());
    }

    public function onMessage(Message $message)
    {

        // TODO: Implement onMessage() method.
    }
}