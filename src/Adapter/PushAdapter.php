<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Adapter;

use Jgut\Tify\Notification;

/**
 * Push adapter interface.
 */
interface PushAdapter extends Adapter
{
    /**
     * Push notification.
     *
     * @param Notification $notification
     *
     * @return \Jgut\Tify\Result[]
     */
    public function push(Notification $notification);
}
