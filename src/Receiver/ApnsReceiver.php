<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Receiver;

/**
 * APNS device receiver.
 */
class ApnsReceiver extends AbstractReceiver
{
    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setToken($token)
    {
        if (!ctype_xdigit($token) || strlen(trim($token)) !== 64) {
            throw new \InvalidArgumentException('APNS token must be a 64 hex string');
        }

        $this->token = trim($token);

        return $this;
    }
}
