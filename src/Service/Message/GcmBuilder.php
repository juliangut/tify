<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Service\Message;

use Jgut\Pushat\Notification\Gcm as GcmNotification;
use Jgut\Pushat\Service\Message\Gcm as Message;

class GcmBuilder
{
    /**
     * Get configured service message.
     *
     * @param array                         $tokens
     * @param \Jgut\Pushat\Notification\Gcm $notification
     *
     * @return \Jgut\Pushat\Service\Message\Gcm
     */
    public static function build(array $tokens, GcmNotification $notification)
    {
        $message = $notification->getMessage();

        $pushMessage = new Message();

        $pushMessage
            ->setRegistrationIds($tokens)
            ->setCollapseKey($notification->getOption('collapse_key'))
            ->setDelayWhileIdle($notification->getOption('delay_while_idle'))
            ->setTimeToLive($notification->getOption('time_to_live'))
            ->setRestrictedPackageName($notification->getOption('restricted_package_name'))
            ->setDryRun($notification->getOption('dry_run'))
            ->setData($message->getParameters());

        if ($message->getOption('title') !== null || $message->getOption('body') !== null) {
            $pushMessage->setNotificationPayload($message->getOptions());
        }

        return $pushMessage;
    }
}
