<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Adapter\Message;

use Jgut\Tify\Notification;
use Jgut\Tify\Adapter\Message\Gcm as GcmMessage;

/**
 * Class GcmMessageBuilder
 */
class GcmMessageBuilder
{
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
    public static function build(array $tokens, Notification $notification)
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
