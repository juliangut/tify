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
use ZendService\Apple\Exception\RuntimeException;

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
        $message = $this->getMockBuilder(ServiceMessage::class)->disableOriginalConstructor()->getMock();
        $message->expects(self::any())->method('getToken')->will(self::returnValue(['aaa']));

        $pushResponse = $this->getMockBuilder(PushResponse::class)->disableOriginalConstructor()->getMock();
        $pushResponse->expects(self::any())->method('getCode')->will(self::returnValue(8));

        $pushClient = $this->getMockBuilder(MessageClient::class)->disableOriginalConstructor()->getMock();
        $pushClient->expects(self::any())->method('send')->will(self::returnValue($pushResponse));

        $feedbackResponse = $this->getMockBuilder(FeedbackResponse::class)->disableOriginalConstructor()->getMock();
        $feedbackResponse->expects(self::any())->method('getToken')->will(self::returnValue('aaa'));

        $feedbackClient = $this->getMockBuilder(FeedbackClient::class)->disableOriginalConstructor()->getMock();
        $feedbackClient->expects(self::any())->method('feedback')->will(self::returnValue([$feedbackResponse]));

        $builder = $this->getMockBuilder(ApnsBuilder::class)->disableOriginalConstructor()->getMock();
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
        $message = $this->getMockBuilder(Message::class)->disableOriginalConstructor()->getMock();

        $receiver = $this->getMockBuilder(ApnsReceiver::class)->disableOriginalConstructor()->getMock();

        $notification = new Notification($message, [$receiver]);
        $results = $this->adapter->push($notification);

        self::assertCount(1, $results);
    }

    public function testFeedback()
    {
        self::assertCount(1, $this->adapter->feedback());
    }

    public function testExceptionErrorCode()
    {
        $reflection = new \ReflectionClass(get_class($this->adapter));
        $method = $reflection->getMethod('getErrorCodeFromException');
        $method->setAccessible(true);

        $exception = new RuntimeException('Server is unavailable; please retry later');
        self::assertEquals(ApnsAdapter::RESPONSE_UNAVAILABLE, $method->invoke($this->adapter, $exception));

        $exception = new RuntimeException('Unknown');
        self::assertEquals(ApnsAdapter::RESPONSE_UNKNOWN_ERROR, $method->invoke($this->adapter, $exception));
    }
}
