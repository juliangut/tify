<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Adapter\Apns;

use Jgut\Tify\Adapter\AbstractAdapter;
use Jgut\Tify\Adapter\FeedbackAdapter;
use Jgut\Tify\Adapter\PushAdapter;
use Jgut\Tify\Exception\AdapterException;
use Jgut\Tify\Exception\NotificationException;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\ApnsReceiver;
use Jgut\Tify\Result;
use ZendService\Apple\Exception\RuntimeException as ApnsRuntimeException;

/**
 * Class ApnsAdapter
 */
class ApnsAdapter extends AbstractAdapter implements PushAdapter, FeedbackAdapter
{
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
    protected static $statusCodes = [
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
    protected static $statusMessages = [
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
     * APNS service builder.
     *
     * @var \Jgut\Tify\Adapter\Apns\ApnsBuilder
     */
    protected $builder;

    /**
     * @var \ZendService\Apple\Apns\Client\Message
     */
    protected $pushClient;

    /**
     * @var \ZendService\Apple\Apns\Client\Feedback
     */
    protected $feedbackClient;

    /**
     * @param array                                    $parameters
     * @param bool                                     $sandbox
     * @param \Jgut\Tify\Adapter\Apns\ApnsBuilder|null $builder
     *
     * @throws \Jgut\Tify\Exception\AdapterException
     */
    public function __construct(array $parameters = [], $sandbox = false, ApnsBuilder $builder = null)
    {
        parent::__construct($parameters, $sandbox);

        $certificatePath = $this->getParameter('certificate');

        if (!file_exists($certificatePath) || !is_readable($certificatePath)) {
            throw new AdapterException(
                sprintf('Certificate file "%s" does not exist or is not readable', $certificatePath)
            );
        }

        // @codeCoverageIgnoreStart
        if ($builder === null) {
            $builder = new ApnsBuilder;
        }
        // @codeCoverageIgnoreEnd
        $this->builder = $builder;
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

        /* @var \ZendService\Apple\Apns\Message $message */
        foreach ($this->getPushMessages($notification) as $message) {
            try {
                $statusCode = $client->send($message)->getCode();
            // @codeCoverageIgnoreStart
            } catch (ApnsRuntimeException $exception) {
                $statusCode = $this->getErrorCodeFromException($exception);
            }
            // @codeCoverageIgnoreEnd

            $pushResults[] = new Result(
                $message->getToken(),
                null,
                self::$statusCodes[$statusCode],
                self::$statusMessages[$statusCode]
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
            $feedbackResults[] = new Result($response->getToken(), $response->getTime());
        }

        $client->close();
        $this->feedbackClient = null;

        return $feedbackResults;
    }

    /**
     * Get opened ServiceClient
     *
     * @throws \Jgut\Tify\Exception\AdapterException
     *
     * @return \ZendService\Apple\Apns\Client\Message
     */
    protected function getPushClient()
    {
        if ($this->pushClient === null) {
            $this->pushClient = $this->builder->buildPushClient(
                $this->getParameter('certificate'),
                $this->getParameter('pass_phrase'),
                $this->sandbox
            );
        }

        return $this->pushClient;
    }

    /**
     * Get opened ServiceFeedbackClient
     *
     * @throws \Jgut\Tify\Exception\AdapterException
     *
     * @return \ZendService\Apple\Apns\Client\Feedback
     */
    protected function getFeedbackClient()
    {
        if ($this->feedbackClient === null) {
            $this->feedbackClient = $this->builder->buildFeedbackClient(
                $this->getParameter('certificate'),
                $this->getParameter('pass_phrase'),
                $this->sandbox
            );
        }

        return $this->feedbackClient;
    }

    /**
     * Get service formatted push messages.
     *
     * @param \Jgut\Tify\Notification $notification
     *
     * @throws \ZendService\Apple\Exception\RuntimeException
     *
     * @return \Generator
     */
    protected function getPushMessages(Notification $notification)
    {
        foreach ($notification->getReceivers() as $receiver) {
            if ($receiver instanceof ApnsReceiver) {
                yield $this->builder->buildPushMessage($receiver, $notification);
            }
        }
    }

    /**
     * Extract error code from exception.
     *
     * @param \ZendService\Apple\Exception\RuntimeException $exception
     *
     * @return int
     */
    protected function getErrorCodeFromException(ApnsRuntimeException $exception)
    {
        $message = $exception->getMessage();

        if (preg_match('/^Server is unavailable/', $message)) {
            return self::RESPONSE_UNAVAILABLE;
        }

        return self::RESPONSE_UNKNOWN_ERROR;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedParameters()
    {
        return ['certificate', 'pass_phrase'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultParameters()
    {
        return [
            'pass_phrase' => null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRequiredParameters()
    {
        return ['certificate'];
    }
}
