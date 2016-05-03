<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Service\Message;

use Jgut\Tify\Notification;
use Jgut\Tify\Recipient\ApnsRecipient;
use ZendService\Apple\Apns\Message as ServiceMessage;
use ZendService\Apple\Apns\Message\Alert as ServiceMessageAlert;

/**
 * Class ApnsMessageBuilder
 */
class ApnsMessageBuilder
{
    /**
     * Get service message from origin.
     *
     * @param \Jgut\Tify\Recipient\ApnsRecipient $recipient
     * @param \Jgut\Tify\Notification            $notification
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return \ZendService\Apple\Apns\Message
     */
    public static function build(ApnsRecipient $recipient, Notification $notification)
    {
        $message = $notification->getMessage();

        $badge = ((int) $notification->getParameter('badge', 0) === 0)
            ? null
            : $notification->getParameter('badge') + (int) $recipient->getData('badge', 0);

        $pushMessage = new ServiceMessage();

        $pushMessage
            ->setId(sha1($recipient->getToken() . $message->getParameter('body')))
            ->setToken($recipient->getToken())
            ->setSound($notification->getParameter('sound'))
            ->setContentAvailable($notification->getParameter('content_available'))
            ->setCategory($notification->getParameter('category'))
            ->setCustom($message->getPayload())
            ->setBadge($badge);

        if ($notification->getParameter('expire') !== null) {
            $pushMessage->setExpire($notification->getParameter('expire'));
        }

        if ($message->getParameter('title') !== null || $message->getParameter('body') !== null) {
            $pushMessage->setAlert(new ServiceMessageAlert(
                $message->getParameter('body'),
                $message->getParameter('action_loc_key'),
                $message->getParameter('loc_key'),
                $message->getParameter('loc_args'),
                $message->getParameter('launch_image'),
                $message->getParameter('title'),
                $message->getParameter('title_loc_key'),
                $message->getParameter('title_loc_args')
            ));
        }

        return $pushMessage;
    }
}
