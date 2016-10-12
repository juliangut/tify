<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Tests\Adapter\Apns;

use Jgut\Tify\Adapter\Apns\DefaultFactory;
use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\ApnsReceiver;
use ZendService\Apple\Apns\Client\Feedback as FeedbackClient;
use ZendService\Apple\Apns\Client\Message as MessageClient;
use ZendService\Apple\Apns\Message as ApnsMessage;

/**
 * Default APNS service factory tests.
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

    /**
     * @expectedException \Jgut\Tify\Exception\AdapterException
     * @expectedExceptionMessageRegExp /(Unable to connect)|(Unable to set local cert chain file)/
     */
    public function testPushClient()
    {
        $client = $this->factory->buildPushClient(__DIR__ . '/../../../files/apns_certificate.pem');

        self::assertInstanceOf(MessageClient::class, $client);
    }

    /**
     * @expectedException \Jgut\Tify\Exception\AdapterException
     * @expectedExceptionMessageRegExp /(Unable to connect)|(Unable to set local cert chain file)/
     */
    public function testFeedbackClient()
    {
        $client = $this->factory->buildFeedbackClient(__DIR__ . '/../../../files/apns_certificate.pem');

        self::assertInstanceOf(FeedbackClient::class, $client);
    }

    public function testPushMessage()
    {
        $receiver = new ApnsReceiver(
            '9a4ecb987ef59c88b12035278b86f26d448835939a4ecb987ef59c88b1203527'
        );
        $message = new Message;
        $urlArgs = ['arg1' => 'val1'];

        $notification = new Notification($message, [$receiver], ['url-args' => $urlArgs, 'expire' => 600]);

        $pushMessage = $this->factory->buildPushMessage($receiver, $notification);

        self::assertInstanceOf(ApnsMessage::class, $pushMessage);
        self::assertEquals($urlArgs, $pushMessage->getUrlArgs());
        self::assertEquals(time() + 600, $pushMessage->getExpire());
        self::assertNull($pushMessage->getAlert());
    }

    public function testAlertPushMessage()
    {
        $receiver = new ApnsReceiver(
            '9a4ecb987ef59c88b12035278b86f26d448835939a4ecb987ef59c88b1203527'
        );
        $message = new Message(['title-loc-key' => 'MESSAGE_TITLE']);

        $notification = new Notification($message, [$receiver], ['content-available' => 1]);

        $pushMessage = $this->factory->buildPushMessage($receiver, $notification);

        self::assertInstanceOf(ApnsMessage::class, $pushMessage);
        self::assertEquals('MESSAGE_TITLE', $pushMessage->getAlert()->getTitleLocKey());
    }
}
