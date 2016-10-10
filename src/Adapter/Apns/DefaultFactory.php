<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Adapter\Apns;

use Jgut\Tify\Exception\AdapterException;
use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\ApnsReceiver;
use ZendService\Apple\Apns\Client\AbstractClient;
use ZendService\Apple\Apns\Client\Feedback as FeedbackClient;
use ZendService\Apple\Apns\Client\Message as PushClient;
use ZendService\Apple\Apns\Message\Alert as ServiceMessageAlert;
use ZendService\Apple\Apns\Message as ServiceMessage;

/**
 * APNS default service factory.
 */
class DefaultFactory implements Factory
{
    /**
     * Alert parameters list.
     *
     * @var array
     */
    protected static $alertParams = [
        'title',
        'body',
        'title-loc-key',
        'title-loc-args',
        'loc-key',
        'loc-args',
        'action-loc-key',
        'launch-image',
    ];

    /**
     * {@inheritdoc}
     *
     * @throws AdapterException
     *
     * @return PushClient
     */
    public function buildPushClient($certificate, $passPhrase = '', $sandbox = false)
    {
        return $this->buildClient(new PushClient, $certificate, $passPhrase, $sandbox);
    }

    /**
     * {@inheritdoc}
     *
     * @throws AdapterException
     *
     * @return FeedbackClient
     */
    public function buildFeedbackClient($certificate, $passPhrase = '', $sandbox = false)
    {
        return $this->buildClient(new FeedbackClient, $certificate, $passPhrase, $sandbox);
    }

    /**
     * Get opened client.
     *
     * @param AbstractClient $client
     * @param string         $certificate
     * @param string         $passPhrase
     * @param bool           $sandbox
     *
     * @throws AdapterException
     *
     * @return AbstractClient|PushClient|FeedbackClient
     *
     * @codeCoverageIgnore
     */
    protected function buildClient(AbstractClient $client, $certificate, $passPhrase = '', $sandbox = false)
    {
        try {
            $client->open(
                (bool) $sandbox ? AbstractClient::SANDBOX_URI : AbstractClient::PRODUCTION_URI,
                $certificate,
                $passPhrase
            );
        } catch (\Exception $exception) {
            throw new AdapterException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $client;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \ZendService\Apple\Exception\RuntimeException
     */
    public function buildPushMessage(ApnsReceiver $receiver, Notification $notification)
    {
        $message = $notification->getMessage();

        $messageId = sha1(
            sprintf(
                '%s%s%s%s',
                $receiver->getToken(),
                $message->getParameter('title'),
                $message->getParameter('body'),
                time()
            )
        );
        $badge = $notification->getParameter('badge') === null ? null : (int) $notification->getParameter('badge');

        $pushMessage = (new ServiceMessage)
            ->setId($messageId)
            ->setToken($receiver->getToken())
            ->setBadge($badge)
            ->setSound($notification->getParameter('sound'))
            ->setCategory($notification->getParameter('category'))
            ->setCustom($message->getPayloadData());

        if ($notification->getParameter('content-available') !== null) {
            $pushMessage->setContentAvailable((int) $notification->getParameter('content-available'));
        }

        if (is_array($notification->getParameter('url-args'))) {
            $pushMessage->setUrlArgs($notification->getParameter('url-args'));
        }

        if ($notification->getParameter('expire') !== null) {
            $pushMessage->setExpire($notification->getParameter('expire'));
        }

        if ($this->shouldHaveAlert($message)) {
            $pushMessage->setAlert(new ServiceMessageAlert(
                $message->getParameter('body'),
                $message->getParameter('action-loc-key'),
                $message->getParameter('loc-key'),
                $message->getParameter('loc-args'),
                $message->getParameter('launch-image'),
                $message->getParameter('title'),
                $message->getParameter('title-loc-key'),
                $message->getParameter('title-loc-args')
            ));
        }

        return $pushMessage;
    }

    /**
     * Message should have alert dictionary.
     *
     * @param Message $message
     *
     * @return bool
     */
    private function shouldHaveAlert(Message $message)
    {
        $shouldHaveAlert = false;

        foreach (static::$alertParams as $parameter) {
            if ($message->hasParameter($parameter)) {
                $shouldHaveAlert = true;

                break;
            }
        }

        return $shouldHaveAlert;
    }
}
