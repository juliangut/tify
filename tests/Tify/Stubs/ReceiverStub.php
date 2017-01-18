<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Tests\Stubs;

use Jgut\Tify\Receiver\Receiver;
use Jgut\Tify\Receiver\Traits\TokenTrait;

/**
 * Receiver device stub.
 */
class ReceiverStub implements Receiver
{
    use TokenTrait;

    /**
     * {@inheritdoc}
     */
    public function setToken($token)
    {
        $this->token = trim($token);

        return $this;
    }
}
