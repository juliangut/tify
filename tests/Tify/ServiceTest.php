<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Tests;

use Jgut\Tify\Adapter\ApnsAdapter;
use Jgut\Tify\Adapter\GcmAdapter;
use Jgut\Tify\Notification;
use Jgut\Tify\Result;
use Jgut\Tify\Service;

/**
 * Notification service tests.
 */
class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Service
     */
    protected $service;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->service = new Service();
    }

    public function testConstruction()
    {
        /* @var GcmAdapter $adapter */
        $adapter = $this->getMockBuilder(GcmAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();

        /* @var Notification $notification */
        $notification = $this->getMockBuilder(Notification::class)
            ->disableOriginalConstructor()
            ->getMock();

        $service = new Service($adapter, $notification);

        self::assertEquals([$adapter], $service->getAdapters());
        self::assertEquals([$notification], $service->getNotifications());
    }

    public function testAccesorsMutators()
    {
        $service = new Service;

        self::assertEmpty($service->getNotifications());

        /* @var Notification $notification */
        $notification = $this->getMockBuilder(Notification::class)
            ->disableOriginalConstructor()
            ->getMock();
        $service->setNotifications([$notification]);
        self::assertCount(1, $service->getNotifications());

        $service->clearNotifications();
        self::assertCount(0, $service->getNotifications());

        self::assertEmpty($service->getAdapters());

        /* @var GcmAdapter $adapter */
        $adapter = $this->getMockBuilder(GcmAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $service->setAdapters([$adapter]);
        self::assertCount(1, $service->getAdapters());

        $service->clearAdapters();
        self::assertCount(0, $service->getAdapters());
    }

    public function testSend()
    {
        $result = new Result('aaa', new \DateTime('now', new \DateTimeZone('UTC')));

        $adapter = $this->getMockBuilder(GcmAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $adapter->expects(self::once())
            ->method('push')
            ->will(self::returnValue([$result]));
        /* @var GcmAdapter $adapter */
        $this->service->addAdapter($adapter);

        /* @var Notification $notification */
        $notification = $this->getMockBuilder(Notification::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->service->clearNotifications();
        $this->service->addNotification($notification);

        $results = $this->service->push();
        self::assertCount(1, $results);
        self::assertEquals($result, $results[0]);
    }

    public function testFeedback()
    {
        $result = new Result('aaa', new \DateTime('now', new \DateTimeZone('UTC')));

        $adapter = $this->getMockBuilder(ApnsAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $adapter->expects(self::once())
            ->method('feedback')
            ->will(self::returnValue([$result]));
        /* @var GcmAdapter $adapter */
        $this->service->addAdapter($adapter);

        $results = $this->service->feedback();
        self::assertCount(1, $results);
        self::assertEquals($result, $results[0]);
    }
}
