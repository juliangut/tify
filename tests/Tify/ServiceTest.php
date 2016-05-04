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
use Jgut\Tify\Result;
use Jgut\Tify\Service;
use Jgut\Tify\Message;
use Jgut\Tify\Notification;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\Service
     */
    protected $service;

    public function setUp()
    {
        $this->service = new Service(
            [$this->getMockForAbstractClass(AbstractAdapter::class, [], '', false)],
            [$this->getMock(Notification::class, [], [], '', false)]
        );
    }

    public function testAccesorsMutators()
    {
        $service = new Service;

        self::assertEmpty($service->getNotifications());
        $service->addNotification($this->getMock(Notification::class, [], [], '', false));
        self::assertCount(1, $service->getNotifications());

        $service->clearNotifications();
        self::assertCount(0, $service->getNotifications());

        self::assertEmpty($service->getAdapters());
        $service->addAdapter($this->getMock(GcmAdapter::class, [], [], '', false));
        self::assertCount(1, $service->getAdapters());

        $service->clearAdapters();
        self::assertCount(0, $service->getAdapters());
    }

    public function testSend()
    {
        $adapter = $this->getMock(GcmAdapter::class, [], [], '', false);
        $adapter->expects(self::once())->method('send');
        $this->service->addAdapter($adapter);

        $result = new Result('aaa', new \DateTime('now', new \DateTimeZone('UTC')));

        $message = $this->getMock(Message::class, [], [], '', false);
        $notification = $this->getMock(Notification::class, [], [$message, []]);
        $notification->expects(self::once())->method('getResults')->will(self::returnValue([$result]));
        $this->service->clearNotifications();
        $this->service->addNotification($notification);

        $results = $this->service->push();
        self::assertCount(1, $results);
        self::assertEquals($result, $results[0]);
    }

    public function testFeedback()
    {
        $adapter = $this->getMock(ApnsAdapter::class, [], [], '', false);
        $adapter->expects(self::once())->method('feedback')->will(self::returnValue(['aaaa']));
        $this->service->addAdapter($adapter);

        self::assertCount(1, $this->service->feedback());
    }
}
