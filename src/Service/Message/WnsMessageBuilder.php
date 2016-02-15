<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Service\Message;

use Jgut\Tify\Notification\WnsNotification;

class WnsMessageBuilder
{
    /**
     * Get configured service message.
     *
     * @param \Jgut\Tify\Notification\WnsNotification $notification
     *
     * @return \Jgut\Tify\Service\Message\WnsMessage
     */
    public static function build(WnsNotification $notification)
    {
        $message = $notification->getMessage();

        $pushMessage = new WnsMessage($notification->getOption('target'), $notification->getOption('class'));

        $pushMessage
            ->setTitle($message->getOption('title'))
            ->setBody($message->getOption('body'))
            ->setUuid($message->getOption('id'))
            ->setNavigateTo($message->getOption('navigate_to'))
            ->setPayload($message->getParameters())
            ->setSound($message->getOption('sound'));

        return $pushMessage;
    }
}
