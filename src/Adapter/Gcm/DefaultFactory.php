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
        'title',
        'body',
        'icon',
        'sound',
        'tag',
        'color',
        'click_action',
        'title_loc_key',
        'title_loc_args',
        'body_loc_key',
        'body_loc_args',
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
            ->setCollapseKey($notification->getParameter('collapse_key'))
            ->setDelayWhileIdle($notification->getParameter('delay_while_idle'))
            ->setTimeToLive($notification->getParameter('time_to_live'))
            ->setRestrictedPackageName($notification->getParameter('restricted_package_name'))
            ->setDryRun($notification->getParameter('dry_run'))
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
            }
        }

        return $shouldHavePayload;
    }
}
