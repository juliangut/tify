<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Notification;

use Jgut\Tify\Notification\AbstractNotification;

/**
 * @covers \Jgut\Tify\Notification\AbstractNotification
 */
class AbstractNotificationTest extends \PHPUnit_Framework_TestCase
{
    protected $service;
    protected $message;
    protected $notification;

    public function setUp()
    {
        $this->service = $this->getMock('\Jgut\Tify\Service\AbstractService', [], [], '', false);
        $this->message = $this->getMock('\Jgut\Tify\Message\AbstractMessage', [], [], '', false);
        $recipient = $this->getMock('\Jgut\Tify\Recipient\AbstractRecipient', [], [], '', false);

        $this->notification = $this->getMockForAbstractClass(
            '\Jgut\Tify\Notification\AbstractNotification',
            [$this->service, $this->message, [$recipient]]
        );
    }

    /**
     * @covers \Jgut\Tify\Notification\AbstractNotification::getService
     * @covers \Jgut\Tify\Notification\AbstractNotification::getMessage
     * @covers \Jgut\Tify\Notification\AbstractNotification::getRecipients
     * @covers \Jgut\Tify\Notification\AbstractNotification::getResult
     * @covers \Jgut\Tify\Notification\AbstractNotification::getTokens
     */
    public function testDefaults()
    {
        $this->assertEquals($this->service, $this->notification->getService());
        $this->assertEquals($this->message, $this->notification->getMessage());
        $this->assertCount(0, $this->notification->getRecipients());
        $this->assertCount(0, $this->notification->getResult());
        $this->assertCount(0, $this->notification->getTokens());
    }

    /**
     * @covers \Jgut\Tify\Notification\AbstractNotification::getStatus
     * @covers \Jgut\Tify\Notification\AbstractNotification::isPushed
     * @covers \Jgut\Tify\Notification\AbstractNotification::setPushed
     * @covers \Jgut\Tify\Notification\AbstractNotification::setPending
     * @covers \Jgut\Tify\Notification\AbstractNotification::getPushTime
     */
    public function testStatus()
    {
        $this->assertEquals(AbstractNotification::STATUS_PENDING, $this->notification->getStatus());
        $this->assertFalse($this->notification->isPushed());
        $this->assertCount(0, $this->notification->getResult());
        $this->assertNull($this->notification->getPushTime());

        $this->notification->setPushed(['pushedRecipient']);
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
