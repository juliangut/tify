<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Tests\Notification;

use Jgut\Pushat\Notification\AbstractNotification;

/**
 * @covers \Jgut\Pushat\Notification\AbstractNotification
 */
class AbstractNotificationTest extends \PHPUnit_Framework_TestCase
{
    protected $service;
    protected $message;
    protected $notification;

    public function setUp()
    {
        $this->service = $this->getMock('\Jgut\Pushat\Service\AbstractService', [], [], '', false);
        $this->message = $this->getMock('\Jgut\Pushat\Message\AbstractMessage', [], [], '', false);
        $this->device = $this->getMock('\Jgut\Pushat\Device\AbstractDevice', [], [], '', false);

        $this->notification = $this->getMockForAbstractClass(
            '\Jgut\Pushat\Notification\AbstractNotification',
            [$this->service, $this->message, [$this->device]]
        );
    }

    /**
     * @covers \Jgut\Pushat\Notification\AbstractNotification::getService
     * @covers \Jgut\Pushat\Notification\AbstractNotification::getMessage
     * @covers \Jgut\Pushat\Notification\AbstractNotification::getDevices
     * @covers \Jgut\Pushat\Notification\AbstractNotification::getResult
     * @covers \Jgut\Pushat\Notification\AbstractNotification::getTokens
     */
    public function testDefaults()
    {
        $this->assertEquals($this->service, $this->notification->getService());
        $this->assertEquals($this->message, $this->notification->getMessage());
        $this->assertCount(0, $this->notification->getDevices());
        $this->assertCount(0, $this->notification->getResult());
        $this->assertCount(0, $this->notification->getTokens());
    }

    /**
     * @covers \Jgut\Pushat\Notification\AbstractNotification::getStatus
     * @covers \Jgut\Pushat\Notification\AbstractNotification::isPushed
     * @covers \Jgut\Pushat\Notification\AbstractNotification::setPushed
     * @covers \Jgut\Pushat\Notification\AbstractNotification::setPending
     * @covers \Jgut\Pushat\Notification\AbstractNotification::getPushTime
     */
    public function testStatus()
    {
        $this->assertEquals(AbstractNotification::STATUS_PENDING, $this->notification->getStatus());
        $this->assertFalse($this->notification->isPushed());
        $this->assertCount(0, $this->notification->getResult());
        $this->assertNull($this->notification->getPushTime());

        $this->notification->setPushed(['pushedDevice']);
        $this->assertEquals(AbstractNotification::STATUS_PUSHED, $this->notification->getStatus());
        $this->assertTrue($this->notification->isPushed());
        $this->assertCount(1, $this->notification->getResult());
        $this->assertInstanceOf('\DateTime', $this->notification->getPushTime());

        $this->notification->setPending();
        $this->assertEquals(AbstractNotification::STATUS_PENDING, $this->notification->getStatus());
        $this->assertFalse($this->notification->isPushed());
        $this->assertCount(0, $this->notification->getResult());
        $this->assertNull($this->notification->getPushTime());
    }
}
