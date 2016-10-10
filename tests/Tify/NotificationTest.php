<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Tests\Notification;

use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\AbstractReceiver;

/**
 * Notification tests.
 */
class NotificationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Message
     */
    protected $message;

    /**
     * @var Notification
     */
    protected $notification;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->message = $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()
            ->getMock();
        $receiver = $this->getMockForAbstractClass(AbstractReceiver::class, [], '', false);

        $this->notification = $this->getMockForAbstractClass(
            Notification::class,
            [$this->message, $receiver]
        );
    }

    public function testDefaults()
    {
        self::assertEquals($this->message, $this->notification->getMessage());
        self::assertCount(1, $this->notification->getReceivers());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidParameter()
    {
        $this->notification->setParameter('made-up-parameter', true);
    }

    public function testMessage()
    {
        /* @var Message $message */
        $message = $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->notification->setMessage($message);
        self::assertEquals($message, $this->notification->getMessage());
    }

    public function testReceivers()
    {
        $receiver = $this->getMockForAbstractClass(AbstractReceiver::class, [], '', false);
        $this->notification->addReceiver($receiver);
        self::assertCount(2, $this->notification->getReceivers());

        $this->notification->clearReceivers();
        self::assertCount(0, $this->notification->getReceivers());
    }
}
