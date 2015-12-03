<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Service\Message;

use Jgut\Pushat\Device\Apns as ApnsDevice;
use Jgut\Pushat\Notification\Apns as ApnsNotification;
use ZendService\Apple\Apns\Message;
use ZendService\Apple\Apns\Message\Alert;

class ApnsBuilder
{
    /**
     * Get service message from origin.
     *
     * @param \Jgut\Pushat\Device\Apns       $device
     * @param \Jgut\Pushat\Notification\Apns $notification
     *
     * @return \ZendService\Apple\Apns\Message
     */
    public static function build(ApnsDevice $device, ApnsNotification $notification)
    {
        $message = $notification->getMessage();

        $badge = ((int) $notification->getOption('badge', 0) === 0)
            ? null :
            $notification->getOption('badge') + (int) $device->getParameter('badge', 0);

        $pushMessage = new Message();

        $pushMessage
            ->setId(sha1($device->getToken() . $message->getOption('body')))
            ->setToken($device->getToken())
            ->setExpire($notification->getOption('expire'))
            ->setSound($notification->getOption('sound'))
            ->setContentAvailable($notification->getOption('content_available'))
            ->setCategory($notification->getOption('category'))
            ->setCustom($message->getParameters());

        if ((int) $notification->getOption('badge') !== 0) {
            $pushMessage->setBadge($notification->getOption('badge') + $device->getParameter('badge', 0));
        }

        if ($message->getOption('title') !== null || $message->getOption('body') !== null) {
            $pushMessage->setAlert(new Alert(
                $message->getOption('body'),
                $message->getOption('action_loc_key'),
                $message->getOption('loc_key'),
                $message->getOption('loc_args'),
                $message->getOption('launch_image'),
                $message->getOption('title'),
                $message->getOption('title_loc_key'),
                $message->getOption('title_loc_args')
            ));
        }

        return $pushMessage;
    }
}
