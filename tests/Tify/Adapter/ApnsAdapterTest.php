<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Tests\Adapter;

use Jgut\Tify\Adapter\Apns\DefaultFactory;
use Jgut\Tify\Adapter\ApnsAdapter;
use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use Jgut\Tify\Receiver\ApnsReceiver;
use Psr\Log\LoggerInterface;
use ZendService\Apple\Apns\Client\Feedback as FeedbackClient;
use ZendService\Apple\Apns\Client\Message as MessageClient;
use ZendService\Apple\Apns\Message as ServiceMessage;
use ZendService\Apple\Apns\Response\Feedback as FeedbackResponse;
use ZendService\Apple\Apns\Response\Message as PushResponse;
use ZendService\Apple\Exception\RuntimeException;

/**
 * APNS service adapter tests.
 */
class ApnsAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApnsAdapter
     */
    protected $adapter;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $pushResponse = $this->getMockBuilder(PushResponse::class)
            ->disableOriginalConstructor()
            ->getMock();
        $pushResponse->expects(self::any())
            ->method('getCode')
            ->will(self::returnValue(ApnsAdapter::RESPONSE_INVALID_TOKEN));

        $pushClient = $this->getMockBuilder(MessageClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $pushClient->expects(self::any())
            ->method('send')
            ->will(self::returnValue($pushResponse));

        $feedbackResponse = $this->getMockBuilder(FeedbackResponse::class)
            ->disableOriginalConstructor()
            ->getMock();
        $feedbackResponse->expects(self::any())
            ->method('getToken')
            ->will(self::returnValue('aaa'));
        $feedbackResponse->expects(self::any())
            ->method('getTime')
            ->will(self::returnValue(2315552));

        $feedbackClient = $this->getMockBuilder(FeedbackClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $feedbackClient->expects(self::any())
            ->method('feedback')
            ->will(self::returnValue([$feedbackResponse]));

        $message = $this->getMockBuilder(ServiceMessage::class)
            ->disableOriginalConstructor()
            ->getMock();
        $message->expects(self::any())
            ->method('getToken')
            ->will(self::returnValue('aaa'));
        $message->expects(self::any())
            ->method('getPayload')
            ->will(self::returnValue(['badge' => 0, 'aps' => []]));

        $factory = $this->getMockBuilder(DefaultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $factory->expects(self::any())
            ->method('buildPushClient')
            ->will(self::returnValue($pushClient));
        $factory->expects(self::any())
            ->method('buildFeedbackClient')
            ->will(self::returnValue($feedbackClient));
        $factory->expects(self::any())
            ->method('buildPushMessage')
            ->will(self::returnValue($message));

        $this->adapter = new ApnsAdapter(
            [ApnsAdapter::PARAMETER_CERTIFICATE => __DIR__ . '/../../files/apns_certificate.pem'],
            true,
            $factory
        );
    }

    /**
     * @expectedException \Jgut\Tify\Exception\AdapterException
     */
    public function testInvalidCertificate()
    {
        new ApnsAdapter(['certificate' => 'fake_path']);
    }

    public function testErrorLogging()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::once())
            ->method('warning')
            ->with($this->matchesRegularExpression('/^Error ".+" sending push notification/'));
        /* @var LoggerInterface $logger */

        $this->adapter->setLogger($logger);

        /* @var Message $message */
        $message = $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()
            ->getMock();

        $receiver = new ApnsReceiver('abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789');

        $notification = new Notification($message, [$receiver]);
        $this->adapter->push($notification);
    }

    public function testSend()
    {
        /* @var Message $message */
        $message = $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()
            ->getMock();

        $receiver = new ApnsReceiver('abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789');

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
