<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Service\Message;

use Jgut\Tify\Notification\MpnsNotification;

class MpnsMessageBuilder
{
    /**
     * Get configured service message.
     *
     * @param \Jgut\Tify\Notification\MpnsNotification $notification
     *
     * @return \Jgut\Tify\Service\Message\MpnsMessage
     */
    public static function build(MpnsNotification $notification)
    {
        $message = $notification->getMessage();

        $pushMessage = new MpnsMessage($notification->getOption('target'), $notification->getOption('class'));

        $pushMessage
            ->setTitle($message->getOption('title'))
            ->setBody($message->getOption('body'))
            ->setUuid($notification->getOption('id'))
            ->setNavigateTo($message->getOption('navigate_to'))
            ->setPayload($message->getParameters())
            ->setCount($message->getOption('count'))
            ->setBackgroundImage($message->getOption('background_image'))
            ->setBackBackgroundImage($message->getOption('back_background_image'))
            ->setSilent($message->getOption('silent'))
            ->setSound($message->getOption('sound'));

        return $pushMessage;
    }
}
