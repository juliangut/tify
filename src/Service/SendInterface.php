<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Service;

use Jgut\Pushat\Notification\AbstractNotification;

interface SendInterface
{
    /**
     * Send a notification.
     *
     * @param \Jgut\Pushat\Notification\AbstractNotification $notification
     */
    public function send(AbstractNotification $notification);
}
