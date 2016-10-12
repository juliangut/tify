<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Adapter\Gcm;

use Jgut\Tify\Message as NotificationMessage;
use Jgut\Tify\Notification;
use Zend\Http\Client\Adapter\Socket;
use Zend\Http\Client as HttpClient;
use ZendService\Google\Gcm\Client as PushClient;

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
        NotificationMessage::PARAMETER_TITLE,
        NotificationMessage::PARAMETER_BODY,
        NotificationMessage::PARAMETER_ICON,
        NotificationMessage::PARAMETER_SOUND,
        NotificationMessage::PARAMETER_TAG,
        NotificationMessage::PARAMETER_COLOR,
        NotificationMessage::PARAMETER_CLICK_ACTION,
        NotificationMessage::PARAMETER_TITLE_LOC_KEY,
        NotificationMessage::PARAMETER_TITLE_LOC_ARGS,
        NotificationMessage::PARAMETER_BODY_LOC_KEY,
        NotificationMessage::PARAMETER_BODY_LOC_ARGS,
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

        $pushMessage = new Message;
        $pushMessage
            ->setRegistrationIds($tokens)
            ->setCollapseKey($notification->getParameter(Notification::PARAMETER_COLLAPSE_KEY))
            ->setDelayWhileIdle($notification->getParameter(Notification::PARAMETER_DELAY_WHILE_IDLE))
            ->setTimeToLive($notification->getParameter(Notification::PARAMETER_TTL))
            ->setRestrictedPackageName($notification->getParameter(Notification::PARAMETER_RESTRICTED_PACKAGE_NAME))
            ->setDryRun($notification->getParameter(Notification::PARAMETER_DRY_RUN))
            ->setData($message->getPayloadData());

        if ($this->shouldHavePayload($message)) {
            $pushMessage->setNotificationPayload($message->getParameters());
        }

        return $pushMessage;
    }

    /**
     * Message should have notification data.
     *
     * @param NotificationMessage $message
     *
     * @return bool
     */
    private function shouldHavePayload(NotificationMessage $message)
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
