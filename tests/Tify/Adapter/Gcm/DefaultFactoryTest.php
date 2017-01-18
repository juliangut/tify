<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Tests\Adapter\Gcm;

use Jgut\Tify\Adapter\Gcm\DefaultFactory;
use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use ZendService\Google\Gcm\Client;
use ZendService\Google\Gcm\Message as ServiceMessage;

/**
 * Default GCM service factory tests.
 */
class DefaultFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultFactory
     */
    protected $factory;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->factory = new DefaultFactory;
    }

    public function testPushClient()
    {
        $client = $this->factory->buildPushClient('my_api_key');

        self::assertInstanceOf(Client::class, $client);
    }

    public function testPushMessage()
    {
        $message = new Message;

        $notification = new Notification($message, [], ['collapse_key' => 'my_key']);

        $pushMessage = $this->factory->buildPushMessage(['my_token'], $notification);

        self::assertInstanceOf(ServiceMessage::class, $pushMessage);
        self::assertEquals('my_key', $pushMessage->getCollapseKey());
    }

    public function testNotificationPushMessage()
    {
        $message = new Message(['title_loc_key' => 'MESSAGE_TITLE']);

        $notification = new Notification($message, []);

        $pushMessage = $this->factory->buildPushMessage(['my_token'], $notification);

        self::assertInstanceOf(ServiceMessage::class, $pushMessage);
        self::assertEquals('MESSAGE_TITLE', $pushMessage->getNotification()['title_loc_key']);
    }
}
