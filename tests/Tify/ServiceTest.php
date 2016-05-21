<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests;

use Jgut\Tify\Adapter\AbstractAdapter;
use Jgut\Tify\Adapter\Apns\ApnsAdapter;
use Jgut\Tify\Adapter\Gcm\GcmAdapter;
use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use Jgut\Tify\Result;
use Jgut\Tify\Service;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\Service
     */
    protected $service;

    public function setUp()
    {
        $this->service = new Service(
            $this->getMockForAbstractClass(AbstractAdapter::class, [], '', false),
            $this->getMockBuilder(Notification::class)->disableOriginalConstructor()->getMock()
        );
    }

    public function testAccesorsMutators()
    {
        $service = new Service;

        self::assertEmpty($service->getNotifications());
        $service->addNotification(
            $this->getMockBuilder(Notification::class)->disableOriginalConstructor()->getMock()
        );
        self::assertCount(1, $service->getNotifications());

        $service->clearNotifications();
        self::assertCount(0, $service->getNotifications());

        self::assertEmpty($service->getAdapters());
        $service->addAdapter(
            $this->getMockBuilder(GcmAdapter::class)->disableOriginalConstructor()->getMock()
        );
        self::assertCount(1, $service->getAdapters());

        $service->clearAdapters();
        self::assertCount(0, $service->getAdapters());
    }

    public function testSend()
    {
        $result = new Result('aaa', new \DateTime('now', new \DateTimeZone('UTC')));

        $adapter = $this->getMockBuilder(GcmAdapter::class)->disableOriginalConstructor()->getMock();
        $adapter->expects(self::once())->method('push')->will(self::returnValue([$result]));
        $this->service->addAdapter($adapter);

        $message = $this->getMockBuilder(Message::class)->disableOriginalConstructor()->getMock();
        $notification = $this->getMockBuilder(Notification::class)->disableOriginalConstructor()->getMock();
        $this->service->clearNotifications();
        $this->service->addNotification($notification);

        $results = $this->service->push();
        self::assertCount(1, $results);
        self::assertEquals($result, $results[0]);
    }

    public function testFeedback()
    {
        $result = new Result('aaa', new \DateTime('now', new \DateTimeZone('UTC')));

        $adapter = $this->getMockBuilder(ApnsAdapter::class)->disableOriginalConstructor()->getMock();
        $adapter->expects(self::once())->method('feedback')->will(self::returnValue([$result]));
        $this->service->addAdapter($adapter);

        $results = $this->service->feedback();
        self::assertCount(1, $results);
        self::assertEquals($result, $results[0]);
    }
}
