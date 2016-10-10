<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Tests\Receiver;

use Jgut\Tify\Receiver\ApnsReceiver;

/**
 * APNS device receiver tests.
 */
class ApnsReceiverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApnsReceiver
     */
    protected $receiver;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->receiver = new ApnsReceiver('9a4ecb987ef59c88b12035278b86f26d448835939a4ecb987ef59c88b1203527');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadToken()
    {
        $this->receiver->setToken('non_hex_short_token');
    }
}
