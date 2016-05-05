<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Adapter\Apns;

use Jgut\Tify\Adapter\Apns\ApnsAdapter;
use Jgut\Tify\Adapter\Apns\ApnsBuilder;
use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\ApnsReceiver;
use ZendService\Apple\Apns\Client\Feedback as FeedbackClient;
use ZendService\Apple\Apns\Client\Message as MessageClient;
use ZendService\Apple\Apns\Message as ServiceMessage;
use ZendService\Apple\Apns\Response\Feedback as FeedbackResponse;
use ZendService\Apple\Apns\Response\Message as PushResponse;

/**
 * Apns adapter tests.
 */
class ApnsAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\Adapter\Apns\ApnsAdapter
     */
    protected $adapter;

    public function setUp()
    {
        $message = $this->getMock(ServiceMessage::class, [], [], '', false);
        $message->expects(self::any())->method('getToken')->will(self::returnValue(['aaa']));

        $pushResponse = $this->getMock(PushResponse::class, [], [], '', false);
        $pushResponse->expects(self::any())->method('getCode')->will(self::returnValue(8));

        $pushClient = $this->getMock(MessageClient::class, [], [], '', false);
        $pushClient->expects(self::any())->method('send')->will(self::returnValue($pushResponse));

        $feedbackResponse = $this->getMock(FeedbackResponse::class, [], [], '', false);
        $feedbackResponse->expects(self::any())->method('getToken')->will(self::returnValue('aaa'));

        $feedbackClient = $this->getMock(FeedbackClient::class, [], [], '', false);
        $feedbackClient->expects(self::any())->method('feedback')->will(self::returnValue([$feedbackResponse]));

        $builder = $this->getMock(ApnsBuilder::class, [], [], '', false);
        $builder->expects(self::any())->method('buildPushClient')->will(self::returnValue($pushClient));
        $builder->expects(self::any())->method('buildFeedbackClient')->will(self::returnValue($feedbackClient));
        $builder->expects(self::any())->method('buildPushMessage')->will(self::returnValue($message));

        $this->adapter = new ApnsAdapter(
            ['certificate' => __DIR__ . '/../../../files/apns_certificate.pem'],
            false,
            $builder
        );
    }

    /**
     * @expectedException \Jgut\Tify\Exception\AdapterException
     */
    public function testInvalidCertificate()
    {
        new ApnsAdapter(['certificate' => 'fake_path']);
    }

    public function testSend()
    {
        $message = $this->getMock(Message::class, [], [], '', false);

        $receiver = $this->getMock(ApnsReceiver::class, [], [], '', false);

        $notification = new Notification($message, [$receiver]);
        $this->adapter->send($notification);

        self::assertCount(1, $notification->getResults());
    }

    public function testFeedback()
    {
        self::assertCount(1, $this->adapter->feedback());
    }
}
