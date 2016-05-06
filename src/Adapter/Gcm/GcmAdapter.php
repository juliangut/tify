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
use Jgut\Tify\Adapter\SendAdapter;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\GcmReceiver;
use Jgut\Tify\Result;
use ZendService\Google\Exception\RuntimeException as GcmRuntimeException;

/**
 * Class GcmAdapter
 */
class GcmAdapter extends AbstractAdapter implements SendAdapter
{
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
        parent::__construct($parameters);

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
    public function send(Notification $notification)
    {
        $client = $this->getPushClient();

        /* @var \ZendService\Google\Gcm\Message $message */
        foreach ($this->getPushMessages($notification) as $message) {
            $time = new \DateTime('now', new \DateTimeZone('UTC'));

            try {
                $pushResponses = $client->send($message)->getResults();

                foreach ($message->getRegistrationIds() as $token) {
                    $result = new Result($token, $time);

                    if (!array_key_exists($token, $pushResponses)
                        || array_key_exists('error', $pushResponses[$token])
                    ) {
                        $result->setStatus(Result::STATUS_ERROR);

                        $errorCode = array_key_exists($token, $pushResponses)
                            ? $pushResponses[$token]['error']
                            : self::RESULT_UNKNOWN;
                        $result->setStatusMessage(self::$statusCodes[$errorCode]);
                    }

                    $notification->addResult($result);
                }
            // @codeCoverageIgnoreStart
            } catch (GcmRuntimeException $exception) {
                foreach ($message->getRegistrationIds() as $token) {
                    $notification->addResult(new Result($token, $time, Result::STATUS_ERROR, $exception->getMessage()));
                }
            }
            // @codeCoverageIgnoreEnd
        }
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
     * Get push service formatted messages.
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
        /* @var \Jgut\Tify\Receiver\GcmReceiver[] $receivers */
        $receivers = array_filter(
            $notification->getReceivers(),
            function ($receiver) {
                return $receiver instanceof GcmReceiver;
            }
        );

        $tokens = array_map(
            function ($receiver) {
                /* @var \Jgut\Tify\Receiver\GcmReceiver $receiver */
                return $receiver->getToken();
            },
            $receivers
        );

        foreach (array_chunk($tokens, 100) as $tokensRange) {
            yield $this->builder->buildPushMessage($tokensRange, $notification);
        }
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
