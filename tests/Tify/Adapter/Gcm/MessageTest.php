<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Tests\Adapter\Gcm;

use Jgut\Tify\Adapter\Gcm\Message;

/**
 * Custom GCM message tests.
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Message
     */
    protected $message;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->message = new Message;
    }

    public function testMutatorsAccessors()
    {
        self::assertCount(0, $this->message->getNotificationPayload());

        $this->message->addNotificationPayload('first', 'first');
        self::assertCount(1, $this->message->getNotificationPayload());

        $this->message->setNotificationPayload(['first' => 'first', 'second' => 'second']);
        self::assertCount(2, $this->message->getNotificationPayload());

        $this->message->clearNotificationPayload();
        self::assertCount(0, $this->message->getNotificationPayload());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidKey()
    {
        $this->message->addNotificationPayload('   ', 'first');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDuplicatedKey()
    {
        $this->message->addNotificationPayload('first', 'first');
        $this->message->addNotificationPayload('first', 'first');
    }

    public function testJsonResult()
    {
        $this->message->setRegistrationIds(['sdfshj']);
        $this->message->setCollapseKey('key');
        $this->message->setDelayWhileIdle(true);
        $this->message->setTimeToLive(600);
        $this->message->setRestrictedPackageName('package.name');
        $this->message->setDryRun(true);
        $this->message->setData(['key' => 'value']);
        $this->message->addNotificationPayload('first', 'first');

        $result = json_decode($this->message->toJson());
        self::assertTrue(isset($result->notification->first));
    }
}
