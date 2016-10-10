<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Adapter;

use Jgut\Tify\Adapter\Gcm\DefaultFactory;
use Jgut\Tify\Adapter\Gcm\Factory;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\GcmReceiver;
use Jgut\Tify\Result;
use ZendService\Google\Exception\RuntimeException as GcmRuntimeException;

/**
 * GCM service adapter.
 */
class GcmAdapter extends AbstractAdapter implements PushAdapter
{
    const RESPONSE_OK = 'OK';
    const RESPONSE_MISSING_REGISTRATION = 'MissingRegistration';
    const RESPONSE_INVALID_REGISTRATION = 'InvalidRegistration';
    const RESPONSE_NOT_REGISTERED = 'NotRegistered';
    const RESPONSE_INVALID_PACKAGE_NAME = 'InvalidPackageName';
    const RESPONSE_MISMATCH_SENDER_ID = 'MismatchSenderId';
    const RESPONSE_MESSAGE_TOO_BIG = 'MessageTooBig';
    const RESPONSE_INVALID_DATA_KEY = 'InvalidDataKey';
    const RESPONSE_INVALID_TTL = 'InvalidTtl';
    const RESPONSE_TIMEOUT = 'Timeout';
    const RESPONSE_INTERNAL_SERVER_ERROR = 'InternalServerError';
    const RESPONSE_DEVICE_MESSAGE_RATE_EXCEEDED = 'DeviceMessageRateExceeded';
    const RESPONSE_TOPIC_MESSAGE_RATE_EXCEEDED = 'TopicsMessageRateExceeded';
    const RESPONSE_SERVER_UNAVAILABLE = 'ServerUnavailable';
    const RESPONSE_AUTHENTICATION_ERROR = 'AuthenticationError';
    const RESPONSE_INVALID_MESSAGE = 'InvalidMessage';
    const RESPONSE_BADLY_FORMATTED_RESPONSE = 'BadlyFormattedResponse';
    const RESPONSE_UNKNOWN_ERROR = 'Unknown';

    /**
     * Status codes mapping.
     *
     * @var array
     */
    protected static $statusCodes = [
        self::RESPONSE_OK => Result::STATUS_SUCCESS,

        self::RESPONSE_MISSING_REGISTRATION => Result::STATUS_INVALID_MESSAGE,
        self::RESPONSE_INVALID_PACKAGE_NAME => Result::STATUS_INVALID_MESSAGE,
        self::RESPONSE_MISMATCH_SENDER_ID => Result::STATUS_INVALID_MESSAGE,
        self::RESPONSE_MESSAGE_TOO_BIG => Result::STATUS_INVALID_MESSAGE,
        self::RESPONSE_INVALID_DATA_KEY => Result::STATUS_INVALID_MESSAGE,
        self::RESPONSE_INVALID_TTL => Result::STATUS_INVALID_MESSAGE,
        self::RESPONSE_INVALID_MESSAGE => Result::STATUS_INVALID_MESSAGE,

        self::RESPONSE_INVALID_REGISTRATION => Result::STATUS_INVALID_DEVICE,
        self::RESPONSE_NOT_REGISTERED => Result::STATUS_INVALID_DEVICE,

        self::RESPONSE_DEVICE_MESSAGE_RATE_EXCEEDED => Result::STATUS_RATE_ERROR,
        self::RESPONSE_TOPIC_MESSAGE_RATE_EXCEEDED => Result::STATUS_RATE_ERROR,

        self::RESPONSE_AUTHENTICATION_ERROR => Result::STATUS_AUTH_ERROR,

        self::RESPONSE_TIMEOUT => Result::STATUS_SERVER_ERROR,
        self::RESPONSE_INTERNAL_SERVER_ERROR => Result::STATUS_SERVER_ERROR,
        self::RESPONSE_SERVER_UNAVAILABLE => Result::STATUS_SERVER_ERROR,
        self::RESPONSE_BADLY_FORMATTED_RESPONSE => Result::STATUS_SERVER_ERROR,

        self::RESPONSE_UNKNOWN_ERROR => Result::STATUS_UNKNOWN_ERROR,
    ];

    /**
     * Status messages mapping.
     *
     * @see https://developers.google.com/cloud-messaging/http-server-ref
     *
     * @var array
     */
    protected static $statusMessages = [
        self::RESPONSE_OK => 'OK',

        self::RESPONSE_MISSING_REGISTRATION => 'Missing Registration Token',
        self::RESPONSE_INVALID_PACKAGE_NAME => 'Invalid Package Name',
        self::RESPONSE_MISMATCH_SENDER_ID => 'Mismatched Sender',
        self::RESPONSE_MESSAGE_TOO_BIG => 'Message Too Big',
        self::RESPONSE_INVALID_DATA_KEY => 'Invalid Data Key',
        self::RESPONSE_INVALID_TTL => 'Invalid Time to Live',
        self::RESPONSE_INVALID_MESSAGE => 'Invalid message',

        self::RESPONSE_INVALID_REGISTRATION => 'Invalid Registration Token',
        self::RESPONSE_NOT_REGISTERED => 'Unregistered Device',

        self::RESPONSE_DEVICE_MESSAGE_RATE_EXCEEDED => 'Device Message Rate Exceeded',
        self::RESPONSE_TOPIC_MESSAGE_RATE_EXCEEDED => 'Topics Message Rate Exceeded',

        self::RESPONSE_AUTHENTICATION_ERROR => 'Authentication Error',

        self::RESPONSE_TIMEOUT => 'Timeout',
        self::RESPONSE_INTERNAL_SERVER_ERROR => 'Internal Server Error',
        self::RESPONSE_SERVER_UNAVAILABLE => 'Server Unavailable',
        self::RESPONSE_BADLY_FORMATTED_RESPONSE => 'Bad Formatted Response',

        self::RESPONSE_UNKNOWN_ERROR => 'Unknown Error',
    ];

    /**
     * GCM service factory.
     *
     * @var Factory
     */
    protected $factory;

    /**
     * Push service client.
     *
     * @var \ZendService\Google\Gcm\Client
     */
    protected $pushClient;

    /**
     * GCM service adapter constructor.
     *
     * @param array   $parameters
     * @param bool    $sandbox
     * @param Factory $factory
     *
     * @throws \Jgut\Tify\Exception\AdapterException
     */
    public function __construct(array $parameters = [], $sandbox = false, Factory $factory = null)
    {
        parent::__construct($parameters, $sandbox);

        // @codeCoverageIgnoreStart
        if ($factory === null) {
            $factory = new DefaultFactory;
        }
        // @codeCoverageIgnoreEnd

        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     * @throws \ZendService\Google\Exception\InvalidArgumentException
     * @throws \ZendService\Google\Exception\RuntimeException
     */
    public function push(Notification $notification)
    {
        $client = $this->getPushClient();

        $pushResults = [];

        foreach ($this->getPushMessage($notification) as $pushMessage) {
            $date = new \DateTime('now', new \DateTimeZone('UTC'));

            try {
                $pushResponses = $client->send($pushMessage)->getResults();

                foreach ($pushMessage->getRegistrationIds() as $token) {
                    $statusCode = self::RESPONSE_OK;

                    if (!array_key_exists($token, $pushResponses)) {
                        $statusCode = self::RESPONSE_UNKNOWN_ERROR;
                    } elseif (array_key_exists('error', $pushResponses[$token])) {
                        $statusCode = $pushResponses[$token]['error'];
                    }

                    $pushResults[] = new Result(
                        $token,
                        $date,
                        self::$statusCodes[$statusCode],
                        self::$statusMessages[$statusCode]
                    );
                }
            // @codeCoverageIgnoreStart
            } catch (GcmRuntimeException $exception) {
                $statusCode = $this->getErrorCodeFromException($exception);

                foreach ($pushMessage->getRegistrationIds() as $token) {
                    $pushResults[] = new Result(
                        $token,
                        $date,
                        self::$statusCodes[$statusCode],
                        self::$statusMessages[$statusCode]
                    );
                }
            }
            // @codeCoverageIgnoreEnd
        }

        return $pushResults;
    }

    /**
     * Get opened push client.
     *
     * @return \ZendService\Google\Gcm\Client
     */
    protected function getPushClient()
    {
        if ($this->pushClient === null) {
            $this->pushClient = $this->factory->buildPushClient($this->getParameter('api_key'));
        }

        return $this->pushClient;
    }

    /**
     * Get service formatted push message.
     *
     * @param Notification $notification
     *
     * @throws \ZendService\Google\Exception\InvalidArgumentException
     * @throws \ZendService\Google\Exception\RuntimeException
     *
     * @return \Generator<\ZendService\Google\Gcm\Message>
     */
    protected function getPushMessage(Notification $notification)
    {
        foreach (array_chunk($notification->getReceivers(), 100) as $receivers) {
            $tokens = array_map(
                function ($receiver) {
                    return $receiver instanceof GcmReceiver ? $receiver->getToken() : null;
                },
                $receivers
            );

            $pushMessage = $this->factory->buildPushMessage(array_filter($tokens), $notification);

            if ($this->isSandbox()) {
                $pushMessage->setDryRun(true);
            }

            yield $pushMessage;
        }
    }

    /**
     * Extract error code from exception.
     *
     * @param GcmRuntimeException $exception
     *
     * @return string
     */
    protected function getErrorCodeFromException(GcmRuntimeException $exception)
    {
        $message = $exception->getMessage();

        if (preg_match('/^500 Internal Server Error/', $message)) {
            return static::RESPONSE_INTERNAL_SERVER_ERROR;
        }

        if (preg_match('/^503 Server Unavailable/', $message)) {
            return static::RESPONSE_SERVER_UNAVAILABLE;
        }

        if (preg_match('/^400 Bad Request/', $message)) {
            return static::RESPONSE_INVALID_MESSAGE;
        }

        if (preg_match('/^401 Forbidden/', $message)) {
            return static::RESPONSE_AUTHENTICATION_ERROR;
        }

        if (preg_match('/^Response body did not contain a valid JSON response$/', $message)) {
            return static::RESPONSE_BADLY_FORMATTED_RESPONSE;
        }

        return static::RESPONSE_UNKNOWN_ERROR;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedParameters()
    {
        return ['api_key'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRequiredParameters()
    {
        return ['api_key'];
    }
}
