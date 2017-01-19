<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Adapter;

use Jgut\Tify\Adapter\Apns\DefaultFactory;
use Jgut\Tify\Adapter\Apns\Factory;
use Jgut\Tify\Adapter\Traits\ParameterTrait;
use Jgut\Tify\Adapter\Traits\SandboxTrait;
use Jgut\Tify\Exception\AdapterException;
use Jgut\Tify\Exception\NotificationException;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\ApnsReceiver;
use Jgut\Tify\Result;
use ZendService\Apple\Exception\RuntimeException as ApnsRuntimeException;

/**
 * APNS service adapter.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApnsAdapter implements PushAdapter, FeedbackAdapter
{
    use ParameterTrait;
    use SandboxTrait;

    const PARAMETER_CERTIFICATE = 'certificate';
    const PARAMETER_PASS_PHRASE = 'pass_phrase';

    const RESPONSE_OK = 0;
    const RESPONSE_PROCESSING_ERROR = 1;
    const RESPONSE_MISSING_DEVICE_TOKEN = 2;
    const RESPONSE_MISSING_TOPIC = 3;
    const RESPONSE_MISSING_PAYLOAD = 4;
    const RESPONSE_INVALID_TOKEN_SIZE = 5;
    const RESPONSE_INVALID_TOPIC_SIZE = 6;
    const RESPONSE_INVALID_PAYLOAD_SIZE = 7;
    const RESPONSE_INVALID_TOKEN = 8;
    const RESPONSE_UNKNOWN_ERROR = 255;
    const RESPONSE_UNAVAILABLE = 2048;

    /**
     * Status codes mapping.
     *
     * @var array
     */
    protected static $statusCodeMap = [
        self::RESPONSE_OK => Result::STATUS_SUCCESS,

        self::RESPONSE_MISSING_DEVICE_TOKEN => Result::STATUS_INVALID_MESSAGE,
        self::RESPONSE_MISSING_TOPIC => Result::STATUS_INVALID_MESSAGE,
        self::RESPONSE_MISSING_PAYLOAD => Result::STATUS_INVALID_MESSAGE,
        self::RESPONSE_INVALID_TOKEN_SIZE => Result::STATUS_INVALID_MESSAGE,
        self::RESPONSE_INVALID_TOPIC_SIZE => Result::STATUS_INVALID_MESSAGE,
        self::RESPONSE_INVALID_PAYLOAD_SIZE => Result::STATUS_INVALID_MESSAGE,

        self::RESPONSE_INVALID_TOKEN => Result::STATUS_INVALID_DEVICE,

        self::RESPONSE_PROCESSING_ERROR => Result::STATUS_SERVER_ERROR,
        self::RESPONSE_UNAVAILABLE => Result::STATUS_SERVER_ERROR,

        self::RESPONSE_UNKNOWN_ERROR => Result::STATUS_UNKNOWN_ERROR,
    ];

    /**
     * Status messages mapping.
     *
     * @var array
     */
    protected static $statusMessageMap = [
        self::RESPONSE_OK => 'OK',

        self::RESPONSE_MISSING_DEVICE_TOKEN => 'Missing Device Token',
        self::RESPONSE_MISSING_TOPIC => 'Missing Topic',
        self::RESPONSE_MISSING_PAYLOAD => 'Missing Payload',
        self::RESPONSE_INVALID_TOKEN_SIZE => 'Invalid Token Size',
        self::RESPONSE_INVALID_TOPIC_SIZE => 'Invalid Topic Size',
        self::RESPONSE_INVALID_PAYLOAD_SIZE => 'Invalid Payload Size',

        self::RESPONSE_INVALID_TOKEN => 'Invalid Token',

        self::RESPONSE_PROCESSING_ERROR => 'Processing Error',
        self::RESPONSE_UNAVAILABLE => 'Server Unavailable',

        self::RESPONSE_UNKNOWN_ERROR => 'Unknown Error',
    ];

    /**
     * APNS service factory.
     *
     * @var Factory
     */
    protected $factory;

    /**
     * Push service client.
     *
     * @var \ZendService\Apple\Apns\Client\Message
     */
    protected $pushClient;

    /**
     * Feedback service client.
     *
     * @var \ZendService\Apple\Apns\Client\Feedback
     */
    protected $feedbackClient;

    /**
     * APNS service adapter constructor.
     *
     * @param array   $parameters
     * @param Factory $factory
     * @param bool    $sandbox
     *
     * @throws AdapterException
     */
    public function __construct(array $parameters = [], $sandbox = false, Factory $factory = null)
    {
        $this->assignParameters($parameters);
        $this->setSandbox($sandbox);

        $certificatePath = $this->getParameter(static::PARAMETER_CERTIFICATE);
        if (!file_exists($certificatePath) || !is_readable($certificatePath)) {
            throw new AdapterException(
                sprintf('Certificate file "%s" does not exist or is not readable', $certificatePath)
            );
        }

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
     * @throws \Jgut\Tify\Exception\AdapterException
     * @throws \ZendService\Apple\Exception\RuntimeException
     */
    public function push(Notification $notification)
    {
        $client = $this->getPushClient();

        $pushResults = [];

        $date = new \DateTime('now', new \DateTimeZone('UTC'));

        foreach ($this->getPushMessage($notification) as $pushMessage) {
            try {
                $statusCode = $client->send($pushMessage)->getCode();
            // @codeCoverageIgnoreStart
            } catch (ApnsRuntimeException $exception) {
                $statusCode = $this->getErrorCodeFromException($exception);
            }
            // @codeCoverageIgnoreEnd

            $pushResults[] = new Result(
                $pushMessage->getToken(),
                $date,
                self::$statusCodeMap[$statusCode],
                self::$statusMessageMap[$statusCode]
            );
        }

        $client->close();
        $this->pushClient = null;

        return $pushResults;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     * @throws \Jgut\Tify\Exception\AdapterException
     * @throws \Jgut\Tify\Exception\NotificationException
     */
    public function feedback()
    {
        $client = $this->getFeedbackClient();

        $feedbackResults = [];

        try {
            /* @var \ZendService\Apple\Apns\Response\Feedback[] $feedbackResponses */
            $feedbackResponses = $client->feedback();
        // @codeCoverageIgnoreStart
        } catch (ApnsRuntimeException $exception) {
            throw new NotificationException($exception->getMessage(), $exception->getCode(), $exception);
        }
        // @codeCoverageIgnoreEnd

        foreach ($feedbackResponses as $response) {
            $feedbackResults[] = new Result(
                $response->getToken(),
                \DateTime::createFromFormat('U', $response->getTime())
            );
        }

        $client->close();
        $this->feedbackClient = null;

        return $feedbackResults;
    }

    /**
     * Get opened push service client.
     *
     * @throws AdapterException
     *
     * @return \ZendService\Apple\Apns\Client\Message
     */
    protected function getPushClient()
    {
        if ($this->pushClient === null) {
            $this->pushClient = $this->factory->buildPushClient(
                $this->getParameter(static::PARAMETER_CERTIFICATE),
                $this->getParameter(static::PARAMETER_PASS_PHRASE),
                $this->sandbox
            );
        }

        return $this->pushClient;
    }

    /**
     * Get opened feedback service client.
     *
     * @throws AdapterException
     *
     * @return \ZendService\Apple\Apns\Client\Feedback
     */
    protected function getFeedbackClient()
    {
        if ($this->feedbackClient === null) {
            $this->feedbackClient = $this->factory->buildFeedbackClient(
                $this->getParameter('certificate'),
                $this->getParameter('pass_phrase'),
                $this->sandbox
            );
        }

        return $this->feedbackClient;
    }

    /**
     * Get service formatted push message.
     *
     * @param Notification $notification
     *
     * @throws \ZendService\Apple\Exception\RuntimeException
     *
     * @return \Generator|\ZendService\Apple\Apns\Message[]
     */
    protected function getPushMessage(Notification $notification)
    {
        foreach ($notification->getReceivers() as $receiver) {
            if ($receiver instanceof ApnsReceiver) {
                yield $this->factory->buildPushMessage($receiver, $notification);
            }
        }
    }

    /**
     * Extract error code from exception.
     *
     * @param ApnsRuntimeException $exception
     *
     * @return int
     */
    protected function getErrorCodeFromException(ApnsRuntimeException $exception)
    {
        $message = $exception->getMessage();

        if (preg_match('/^Server is unavailable/', $message)) {
            return static::RESPONSE_UNAVAILABLE;
        }

        return static::RESPONSE_UNKNOWN_ERROR;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedParameters()
    {
        return [static::PARAMETER_CERTIFICATE, static::PARAMETER_PASS_PHRASE];
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultParameters()
    {
        return [
            static::PARAMETER_PASS_PHRASE => null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRequiredParameters()
    {
        return [static::PARAMETER_CERTIFICATE];
    }

    /**
     * Disconnect clients.
     *
     * @codeCoverageIgnore
     */
    public function __destruct()
    {
        if ($this->pushClient !== null && $this->pushClient->isConnected()) {
            $this->pushClient->close();
        }

        if ($this->feedbackClient !== null && $this->feedbackClient->isConnected()) {
            $this->feedbackClient->close();
        }
    }
}
