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
use Jgut\Tify\Message as NotificationMessage;
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
        NotificationMessage::PARAMETER_TITLE,
        NotificationMessage::PARAMETER_BODY,
        NotificationMessage::PARAMETER_TITLE_LOC_KEY,
        NotificationMessage::PARAMETER_TITLE_LOC_ARGS,
        NotificationMessage::PARAMETER_BODY_LOC_KEY,
        NotificationMessage::PARAMETER_BODY_LOC_ARGS,
        NotificationMessage::PARAMETER_ACTION_LOC_KEY,
        NotificationMessage::PARAMETER_LAUNCH_IMAGE,
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
    public function buildFeedbackClient($certificate, $passPhrase = null, $sandbox = false)
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
                $message->getParameter(NotificationMessage::PARAMETER_TITLE),
                $message->getParameter(NotificationMessage::PARAMETER_BODY),
                time()
            )
        );
        $badge = $notification->getParameter(Notification::PARAMETER_BADGE) === null
            ? null
            : (int) $notification->getParameter(Notification::PARAMETER_BADGE);

        $pushMessage = (new ServiceMessage)
            ->setId($messageId)
            ->setToken($receiver->getToken())
            ->setBadge($badge)
            ->setSound($notification->getParameter(Notification::PARAMETER_SOUND))
            ->setCategory($notification->getParameter(Notification::PARAMETER_CATEGORY))
            ->setCustom($message->getPayloadData());

        if ($notification->getParameter(Notification::PARAMETER_CONTENT_AVAILABLE) !== null) {
            $pushMessage->setContentAvailable(
                (int) $notification->getParameter(Notification::PARAMETER_CONTENT_AVAILABLE)
            );
        }

        if (is_array($notification->getParameter(Notification::PARAMETER_URL_ARGS))) {
            $pushMessage->setUrlArgs($notification->getParameter(Notification::PARAMETER_URL_ARGS));
        }

        if ($notification->getParameter(Notification::PARAMETER_TTL) !== null) {
            $expire = time() + (int) $notification->getParameter(Notification::PARAMETER_TTL);

            $pushMessage->setExpire($expire);
        }

        if ($this->shouldHaveAlert($message)) {
            $pushMessage->setAlert(new ServiceMessageAlert(
                $message->getParameter(NotificationMessage::PARAMETER_BODY),
                $message->getParameter(NotificationMessage::PARAMETER_ACTION_LOC_KEY),
                $message->getParameter(NotificationMessage::PARAMETER_BODY_LOC_KEY),
                $message->getParameter(NotificationMessage::PARAMETER_BODY_LOC_ARGS),
                $message->getParameter(NotificationMessage::PARAMETER_LAUNCH_IMAGE),
                $message->getParameter(NotificationMessage::PARAMETER_TITLE),
                $message->getParameter(NotificationMessage::PARAMETER_TITLE_LOC_KEY),
                $message->getParameter(NotificationMessage::PARAMETER_TITLE_LOC_ARGS)
            ));
        }

        return $pushMessage;
    }

    /**
     * Message should have alert dictionary.
     *
     * @param NotificationMessage $message
     *
     * @return bool
     */
    private function shouldHaveAlert(NotificationMessage $message)
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
