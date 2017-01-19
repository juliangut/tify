<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Receiver;

use Jgut\Tify\Receiver\Traits\TokenTrait;

/**
 * FCM device receiver receiver.
 */
final class FcmReceiver implements Receiver
{
    use TokenTrait;

    /**
     * FCM device receiver constructor.
     *
     * @param string $token
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($token)
    {
        $this->setToken($token);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setToken($token)
    {
        $token = trim($token);

        if ($token === '') {
            throw new \InvalidArgumentException('GCM token can not be empty');
        }

        $this->token = $token;

        return $this;
    }
}
