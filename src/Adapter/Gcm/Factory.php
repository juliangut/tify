<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Adapter\Gcm;

use Jgut\Tify\Notification;

/**
 * GCM service factory.
 */
interface Factory
{
    /**
     * Get opened push service client.
     *
     * @param string $apiKey
     *
     * @return \ZendService\Google\Gcm\Client
     */
    public function buildPushClient($apiKey);

    /**
     * Get configured service message.
     *
     * @param array        $tokens
     * @param Notification $notification
     *
     * @return \ZendService\Google\Gcm\Message
     */
    public function buildPushMessage(array $tokens, Notification $notification);
}
