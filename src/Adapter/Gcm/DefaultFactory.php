<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Adapter\Gcm;

use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use Zend\Http\Client\Adapter\Socket;
use Zend\Http\Client as HttpClient;
use ZendService\Google\Gcm\Client as PushClient;
use ZendService\Google\Gcm\Message as ServiceMessage;

/**
 * GCM default service factory.
 */
class DefaultFactory implements Factory
{
    /**
     * Notification parameters list.
     *
     * @var array
     */
    protected static $notificationParams = [
        Message::PARAMETER_TITLE,
        Message::PARAMETER_BODY,
        Message::PARAMETER_ICON,
        Message::PARAMETER_SOUND,
        Message::PARAMETER_TAG,
        Message::PARAMETER_COLOR,
        Message::PARAMETER_CLICK_ACTION,
        Message::PARAMETER_TITLE_LOC_KEY,
        Message::PARAMETER_TITLE_LOC_ARGS,
        Message::PARAMETER_BODY_LOC_KEY,
        Message::PARAMETER_BODY_LOC_ARGS,
    ];

    /**
     * {@inheritdoc}
     */
    public function buildPushClient($apiKey)
    {
        $client = new PushClient;
        $client->setApiKey($apiKey);

        $httpClient = new HttpClient(
            null,
            [
                'service' => Socket::class,
                'strictredirects' => true,
                'sslverifypeer' => false,
            ]
        );

        $client->setHttpClient($httpClient);

        return $client;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \ZendService\Google\Exception\InvalidArgumentException
     * @throws \ZendService\Google\Exception\RuntimeException
     */
    public function buildPushMessage(array $tokens, Notification $notification)
    {
        $message = $notification->getMessage();

        $pushMessage = (new ServiceMessage)
            ->setRegistrationIds($tokens)
            ->setPriority($notification->getParameter(Notification::PARAMETER_PRIORITY))
            ->setCollapseKey($notification->getParameter(Notification::PARAMETER_COLLAPSE_KEY))
            ->setDelayWhileIdle($notification->getParameter(Notification::PARAMETER_DELAY_WHILE_IDLE))
            ->setTimeToLive($notification->getParameter(Notification::PARAMETER_TTL))
            ->setRestrictedPackageName($notification->getParameter(Notification::PARAMETER_RESTRICTED_PACKAGE_NAME))
            ->setDryRun($notification->getParameter(Notification::PARAMETER_DRY_RUN))
            ->setData($message->getPayloadData());

        if ($this->shouldAddNotification($message)) {
            $pushMessage->setNotification($message->getParameters());
        }

        return $pushMessage;
    }

    /**
     * Message should have notification data.
     *
     * @param Message $message
     *
     * @return bool
     */
    private function shouldAddNotification(Message $message)
    {
        $shouldHavePayload = false;

        foreach (static::$notificationParams as $parameter) {
            if ($message->hasParameter($parameter)) {
                $shouldHavePayload = true;

                break;
            }
        }

        return $shouldHavePayload;
    }
}
