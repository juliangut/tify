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
 * APNS device receiver.
 */
final class ApnsReceiver implements Receiver
{
    use TokenTrait;

    /**
     * Constructor.
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
        if (!ctype_xdigit($token) || strlen($token) !== 64) {
            throw new \InvalidArgumentException('APNS token must be a 64 hex string');
        }

        $this->token = trim($token);

        return $this;
    }
}
