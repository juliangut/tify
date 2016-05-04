<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Adapter\Gcm;

use Jgut\Tify\Notification;
use Zend\Http\Client\Adapter\Socket;
use Zend\Http\Client as HttpClient;
use ZendService\Google\Gcm\Client;

/**
 * Class GcmBuilder
 */
class GcmBuilder
{
    /**
     * Get opened push service client.
     *
     * @param string $apiKey
     *
     * @return \ZendService\Google\Gcm\Client
     */
    public function buildPushClient($apiKey)
    {
        $client = new Client;
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
     * Get configured service message.
     *
     * @param array                   $tokens
     * @param \Jgut\Tify\Notification $notification
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return \ZendService\Google\Gcm\Message
     */
    public function buildPushMessage(array $tokens, Notification $notification)
    {
        $message = $notification->getMessage();

        $pushMessage = new GcmMessage();

        $pushMessage
            ->setRegistrationIds($tokens)
            ->setCollapseKey($notification->getParameter('collapse_key'))
            ->setDelayWhileIdle($notification->getParameter('delay_while_idle'))
            ->setTimeToLive($notification->getParameter('time_to_live'))
            ->setRestrictedPackageName($notification->getParameter('restricted_package_name'))
            ->setDryRun($notification->getParameter('dry_run'))
            ->setData($message->getPayload());

        if ($message->getParameter('title') !== null || $message->getParameter('body') !== null) {
            $pushMessage->setNotificationPayload($message->getParameters());
        }

        return $pushMessage;
    }
}
