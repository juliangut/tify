<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Tests\Receiver;

use Jgut\Tify\Receiver\GcmReceiver;

/**
 * GCM device receiver tests.
 */
class GcmReceiverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GcmReceiver
     */
    protected $receiver;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->receiver = new GcmReceiver('f59c88b12035278b86f26d448835939a');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadToken()
    {
        $this->receiver->setToken('    ');
    }
}
