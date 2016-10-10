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
 * Receiver device interface.
 */
interface Receiver
{
    /**
     * Get token.
     *
     * @return string
     */
    public function getToken();

    /**
     * Set token.
     *
     * @param string $token
     */
    public function setToken($token);
}
