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
use Jgut\Tify\Adapter\SendAdapter;
use Jgut\Tify\Exception\AdapterException;
use Jgut\Tify\Exception\NotificationException;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\ApnsReceiver;
use Jgut\Tify\Result;
use ZendService\Apple\Exception\RuntimeException as ApnsRuntimeException;

/**
 * Class ApnsAdapter
 */
class ApnsAdapter extends AbstractAdapter implements SendAdapter, FeedbackAdapter
{
    const RESULT_OK = 0;
    const RESULT_UNAVAILABLE = 2048;

    /**
     * Status codes mapping.
     *
     * @var array
     */
    protected static $statusCodes = [
        0 => 'OK',
        1 => 'Processing Error',
        2 => 'Missing Device Token',
        3 => 'Missing Topic',
        4 => 'Missing Payload',
        5 => 'Invalid Token Size',
        6 => 'Invalid Topic Size',
        7 => 'Invalid Payload Size',
        8 => 'Invalid Token',
        10 => 'Shutdown',
        255 => 'Unknown Error',
        self::RESULT_UNAVAILABLE => 'Server Unavailable',

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

        if ($certificatePath === null || !file_exists($certificatePath) || !is_readable($certificatePath)) {
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
    public function send(Notification $notification)
    {
        $client = $this->getPushClient();

        /* @var \ZendService\Apple\Apns\Message $message */
        foreach ($this->getPushMessages($notification) as $message) {
            $pushResult = new Result($message->getToken());

            try {
                $pushResponse = $client->send($message);

                if ($pushResponse->getCode() !== static::RESULT_OK) {
                    $pushResult->setStatus(Result::STATUS_ERROR);
                    $pushResult->setStatusMessage(self::$statusCodes[$pushResponse->getCode()]);
                }
            // @codeCoverageIgnoreStart
            } catch (ApnsRuntimeException $exception) {
                $pushResult->setStatus(Result::STATUS_ERROR);

                if (preg_match('/^Server is unavailable/', $exception->getMessage())) {
                    $pushResult->setStatusMessage(self::$statusCodes[self::RESULT_UNAVAILABLE]);
                } else {
                    $pushResult->setStatusMessage($exception->getMessage());
                }
            }
            // @codeCoverageIgnoreEnd

            $notification->addResult($pushResult);
        }

        $client->close();
        $this->pushClient = null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Jgut\Tify\Exception\AdapterException
     * @throws \Jgut\Tify\Exception\NotificationException
     */
    public function feedback()
    {
        $client = $this->getFeedbackClient();

        try {
            /* @var \ZendService\Apple\Apns\Response\Feedback[] $feedbackResponses */
            $feedbackResponses = $client->feedback();
        // @codeCoverageIgnoreStart
        } catch (ApnsRuntimeException $exception) {
            throw new NotificationException($exception->getMessage(), $exception->getCode(), $exception);
        }
        // @codeCoverageIgnoreEnd

        $responses = [];

        foreach ($feedbackResponses as $response) {
            $responses[] = $response->getToken();
        }

        $client->close();
        $this->feedbackClient = null;

        return $responses;
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
     * Get push service formatted messages.
     *
     * @param \Jgut\Tify\Notification $notification
     *
     * @throws \ZendService\Apple\Exception\RuntimeException
     *
     * @return \Generator
     */
    protected function getPushMessages(Notification $notification)
    {
        /* @var \Jgut\Tify\Receiver\ApnsReceiver[] $receivers */
        $receivers = array_filter(
            $notification->getReceivers(),
            function ($receiver) {
                return $receiver instanceof ApnsReceiver;
            }
        );

        foreach ($receivers as $receiver) {
            yield $this->builder->buildPushMessage($receiver, $notification);
        }
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
