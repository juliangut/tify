<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Adapter\Apns;

use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\ApnsReceiver;

/**
 * APNS service factory interface.
 */
interface Factory
{
    /**
     * Get opened push service client.
     *
     * @param string $certificate
     * @param string $passPhrase
     * @param bool   $sandbox
     *
     * @return \ZendService\Apple\Apns\Client\Message
     */
    public function buildPushClient($certificate, $passPhrase = '', $sandbox = false);

    /**
     * Get service message from origin.
     *
     * @param ApnsReceiver $receiver
     * @param Notification $notification
     *
     * @return \ZendService\Apple\Apns\Client\Message
     */
    public function buildPushMessage(ApnsReceiver $receiver, Notification $notification);

    /**
     * Get opened feedback service client.
     *
     * @param string $certificate
     * @param string $passPhrase
     * @param bool   $sandbox
     *
     * @return \ZendService\Apple\Apns\Client\Feedback
     */
    public function buildFeedbackClient($certificate, $passPhrase = '', $sandbox = false);
}
