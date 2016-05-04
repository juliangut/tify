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
use Jgut\Tify\Recipient\ApnsRecipient;
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
        $recipient = new ApnsRecipient(
            '9a4ecb987ef59c88b12035278b86f26d448835939a4ecb987ef59c88b1203527'
        );

        $message = new Message(['title' => 'title']);

        $notification = new Notification($message, [], ['expire' => 600, 'badge' => 1]);

        $client = $this->builder->buildPushMessage($recipient, $notification);
        self::assertInstanceOf(ApnsMessage::class, $client);
    }
}
