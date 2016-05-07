<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Adapter;

use Jgut\Tify\Notification;

/**
 * Interface PushAdapter
 */
interface PushAdapter
{
    /**
     * Push notification.
     *
     * @param \Jgut\Tify\Notification $notification
     *
     * @return \Jgut\Tify\Result[]
     */
    public function push(Notification $notification);
}
