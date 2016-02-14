<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Service\Message;

use Jgut\Tify\Recipient\Apns as ApnsRecipient;
use Jgut\Tify\Notification\Apns as ApnsNotification;
use ZendService\Apple\Apns\Message as ServiceMessage;
use ZendService\Apple\Apns\Message\Alert as ServiceMessageAlert;

class ApnsBuilder
{
    /**
     * Get service message from origin.
     *
     * @param \Jgut\Tify\Recipient\Apns    $recipient
     * @param \Jgut\Tify\Notification\Apns $notification
     *
     * @return \ZendService\Apple\Apns\Message
     */
    public static function build(ApnsRecipient $recipient, ApnsNotification $notification)
    {
        $message = $notification->getMessage();

        $badge = ((int) $notification->getOption('badge', 0) === 0)
            ? null
            : $notification->getOption('badge') + (int) $recipient->getParameter('badge', 0);

        $pushMessage = new ServiceMessage();

        $pushMessage
            ->setId(sha1($recipient->getToken() . $message->getOption('body')))
            ->setToken($recipient->getToken())
            ->setSound($notification->getOption('sound'))
            ->setContentAvailable($notification->getOption('content_available'))
            ->setCategory($notification->getOption('category'))
            ->setCustom($message->getParameters())
            ->setBadge($badge);

        if (!is_null($notification->getOption('expire'))) {
            $pushMessage->setExpire($notification->getOption('expire'));
        }

        if ($message->getOption('title') !== null || $message->getOption('body') !== null) {
            $pushMessage->setAlert(new ServiceMessageAlert(
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
