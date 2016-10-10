<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Tests\Receiver;

use Jgut\Tify\Receiver\AbstractReceiver;

/**
 * AbstractReceiver tests.
 */
class AbstractReceiverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractReceiver
     */
    protected $receiver;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->receiver = $this->getMockForAbstractClass(
            AbstractReceiver::class,
            ['9a4ecb987ef59c88b12035278b86f26d44883593']
        );
    }

    public function testDefaults()
    {
        self::assertNull($this->receiver->getToken());
    }
}
