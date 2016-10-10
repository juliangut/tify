<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Adapter;

/**
 * Service adapter interface.
 */
interface Adapter
{
    /**
     * Retrieve if sandbox.
     *
     * @return bool
     */
    public function isSandbox();

    /**
     * Set Sandbox.
     *
     * @param bool $sandbox
     */
    public function setSandbox($sandbox = true);
}
