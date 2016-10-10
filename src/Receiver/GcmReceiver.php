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
 * GCM device receiver.
 */
class GcmReceiver extends AbstractReceiver
{
    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setToken($token)
    {
        if (trim($token) === '') {
            throw new \InvalidArgumentException('GCM token can not be empty');
        }

        $this->token = trim($token);

        return $this;
    }
}
