<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Adapter\Apns;

use Jgut\Tify\Adapter\Apns\ApnsBuilder;
use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\ApnsReceiver;
use ZendService\Apple\Apns\Client\Feedback as FeedbackClient;
use ZendService\Apple\Apns\Client\Message as MessageClient;
use ZendService\Apple\Apns\Message as ApnsMessage;

/**
 * Apns service builder
 */
class ApnsBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\Adapter\Apns\ApnsBuilder
     */
    protected $builder;

    public function setUp()
    {
        $this->builder = new ApnsBuilder;
    }

    /**
     * @expectedException \Jgut\Tify\Exception\AdapterException
     * @expectedExceptionMessageRegExp /^Unable to connect/
     * @expectedExceptionMessageRegExp /^Unable to set local cert chain file/
     */
    public function testPushClient()
    {
        $client = $this->builder->buildPushClient(__DIR__ . '/../../../files/apns_certificate.pem');

        self::assertInstanceOf(MessageClient::class, $client);
    }

    /**
     * @expectedException \Jgut\Tify\Exception\AdapterException
     * @expectedExceptionMessageRegExp /^Unable to connect/
     * @expectedExceptionMessageRegExp /^Unable to set local cert chain file/
     */
    public function testFeedbackClient()
    {
        $client = $this->builder->buildFeedbackClient(__DIR__ . '/../../../files/apns_certificate.pem');

        self::assertInstanceOf(FeedbackClient::class, $client);
    }

    public function testPushMessage()
    {
        $receiver = new ApnsReceiver(
            '9a4ecb987ef59c88b12035278b86f26d448835939a4ecb987ef59c88b1203527'
        );
        $message = new Message();
        $urlArgs = ['arg1' => 'val1'];

        $notification = new Notification($message, [$receiver], ['url-args' => $urlArgs, 'expire' => 600]);

        $pushMessage = $this->builder->buildPushMessage($receiver, $notification);

        self::assertInstanceOf(ApnsMessage::class, $pushMessage);
        self::assertEquals($urlArgs, $pushMessage->getUrlArgs());
        self::assertEquals(600, $pushMessage->getExpire());
        self::assertNull($pushMessage->getAlert());
    }

    public function testAlertPushMessage()
    {
        $receiver = new ApnsReceiver(
            '9a4ecb987ef59c88b12035278b86f26d448835939a4ecb987ef59c88b1203527'
        );
        $message = new Message(['title-loc-key' => 'MESSAGE_TITLE']);

        $notification = new Notification($message, [$receiver]);

        $pushMessage = $this->builder->buildPushMessage($receiver, $notification);

        self::assertInstanceOf(ApnsMessage::class, $pushMessage);
        self::assertEquals('MESSAGE_TITLE', $pushMessage->getAlert()->getTitleLocKey());
    }
}
