<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Adapter\Gcm;

use Jgut\Tify\Adapter\AbstractAdapter;
use Jgut\Tify\Adapter\PushAdapter;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\GcmReceiver;
use Jgut\Tify\Result;
use ZendService\Google\Exception\RuntimeException as GcmRuntimeException;

/**
 * Class GcmAdapter
 */
class GcmAdapter extends AbstractAdapter implements PushAdapter
{
    const RESULT_INTERNAL_SERVER_ERROR = 'InternalServerError';
    const RESULT_SERVER_UNAVAILABLE = 'ServerUnavailable';
    const RESULT_AUTHENTICATION_ERROR = 'AuthenticationError';
    const RESULT_INVALID_MESSAGE = 'InvalidMessage';
    const RESULT_BAD_FORMATTED_RESPONSE = 'BadFormattedResponse';
    const RESULT_UNKNOWN = 'Unknown';

    /**
     * Status codes mapping.
     *
     * @see https://developers.google.com/cloud-messaging/http-server-ref
     *
     * @var array
     */
    protected static $statusCodes = [
        'MissingRegistration' => 'Missing Registration Token',
        'InvalidRegistration' => 'Invalid Registration Token',
        'NotRegistered' => 'Unregistered Device',
        'InvalidPackageName' => 'Invalid Package Name',
        'MismatchSenderId' => 'Mismatched Sender',
        'MessageTooBig' => 'Message Too Big',
        'InvalidDataKey' => 'Invalid Data Key',
        'InvalidTtl' => 'Invalid Time to Live',
        'Unavailable' => 'Timeout',
        'InternalServerError' => 'Internal Server Error',
        'DeviceMessageRateExceeded' => 'Device Message Rate Exceeded',
        'TopicsMessageRateExceeded' => 'Topics Message Rate Exceeded',
        self::RESULT_INTERNAL_SERVER_ERROR => 'Internal Server Error',
        self::RESULT_SERVER_UNAVAILABLE => 'Server Unavailable',
        self::RESULT_AUTHENTICATION_ERROR => 'Authentication Error',
        self::RESULT_INVALID_MESSAGE => 'Invalid message',
        self::RESULT_BAD_FORMATTED_RESPONSE => 'Bad Formatted Response',
        self::RESULT_UNKNOWN => 'Unknown Error',
    ];

    /**
     * GCM service builder.
     *
     * @var \Jgut\Tify\Adapter\Gcm\GcmBuilder
     */
    protected $builder;

    /**
     * @var \ZendService\Google\Gcm\Client
     */
    protected $pushClient;

    /**
     * @param array                                  $parameters
     * @param \Jgut\Tify\Adapter\Gcm\GcmBuilder|null $builder
     *
     * @throws \Jgut\Tify\Exception\AdapterException
     */
    public function __construct(array $parameters = [], GcmBuilder $builder = null)
    {
        parent::__construct($parameters, false);

        // @codeCoverageIgnoreStart
        if ($builder === null) {
            $builder = new GcmBuilder;
        }
        // @codeCoverageIgnoreEnd
        $this->builder = $builder;
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

        /* @var \ZendService\Google\Gcm\Message $message */
        foreach ($this->getPushMessages($notification) as $message) {
            $date = new \DateTime('now', new \DateTimeZone('UTC'));

            try {
                $pushResponses = $client->send($message)->getResults();

                foreach ($message->getRegistrationIds() as $token) {
                    $pushResult = new Result($token, $date);

                    if (!array_key_exists($token, $pushResponses)
                        || array_key_exists('error', $pushResponses[$token])
                    ) {
                        $pushResult->setStatus(Result::STATUS_ERROR);

                        $errorCode = array_key_exists($token, $pushResponses)
                            ? $pushResponses[$token]['error']
                            : self::RESULT_UNKNOWN;
                        $pushResult->setStatusMessage(self::$statusCodes[$errorCode]);
                    }

                    $pushResults[] = $pushResult;
                }
            // @codeCoverageIgnoreStart
            } catch (GcmRuntimeException $exception) {
                $errorMessage = self::$statusCodes[$this->getErrorCodeFromException($exception)];

                foreach ($message->getRegistrationIds() as $token) {
                    $pushResults[] = new Result($token, $date, Result::STATUS_ERROR, $errorMessage);
                }
            }
            // @codeCoverageIgnoreEnd
        }

        return $pushResults;
    }

    /**
     * Get opened client.
     *
     * @return \ZendService\Google\Gcm\Client
     */
    protected function getPushClient()
    {
        if ($this->pushClient === null) {
            $this->pushClient = $this->builder->buildPushClient($this->getParameter('api_key'));
        }

        return $this->pushClient;
    }

    /**
     * Get service formatted push messages.
     *
     * @param \Jgut\Tify\Notification $notification
     *
     * @throws \ZendService\Google\Exception\InvalidArgumentException
     * @throws \ZendService\Google\Exception\RuntimeException
     *
     * @return \Generator
     */
    protected function getPushMessages(Notification $notification)
    {
        foreach (array_chunk($notification->getReceivers(), 100) as $receivers) {
            $tokens = array_map(
                function ($receiver) {
                    return $receiver instanceof GcmReceiver ? $receiver->getToken() : null;
                },
                $receivers
            );

            yield $this->builder->buildPushMessage(array_filter($tokens), $notification);
        }
    }

    /**
     * Extract error code from exception.
     *
     * @param \ZendService\Google\Exception\RuntimeException $exception
     *
     * @return string
     */
    protected function getErrorCodeFromException(GcmRuntimeException $exception)
    {
        $message = $exception->getMessage();

        if (preg_match('/^500 Internal Server Error/', $message)) {
            return self::RESULT_INTERNAL_SERVER_ERROR;
        }

        if (preg_match('/^503 Server Unavailable/', $message)) {
            return self::RESULT_SERVER_UNAVAILABLE;
        }

        if (preg_match('/^401 Forbidden/', $message)) {
            return self::RESULT_AUTHENTICATION_ERROR;
        }

        if (preg_match('/^400 Bad Request/', $message)) {
            return self::RESULT_INVALID_MESSAGE;
        }

        if (preg_match('/^Response body did not contain a valid JSON response$/', $message)) {
            return self::RESULT_BAD_FORMATTED_RESPONSE;
        }

        return self::RESULT_UNKNOWN;
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
