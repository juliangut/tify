<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Service;

use Jgut\Tify\Notification;

/**
 * Interface SendInterface
 */
interface SendInterface
{
    /**
     * Send a notification.
     *
     * @param \Jgut\Tify\Notification $notification
     */
    public function send(Notification $notification);
}
