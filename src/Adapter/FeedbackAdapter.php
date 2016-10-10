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
 * Feedback adapter interface.
 */
interface FeedbackAdapter extends Adapter
{
    /**
     * Request feedback information.
     *
     * @return \Jgut\Tify\Result[]
     */
    public function feedback();
}
