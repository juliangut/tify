<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Notification;

use Jgut\Tify\Notification\GcmNotification;

/**
 * @covers \Jgut\Tify\Notification\GcmNotification
 */
class GcmTest extends \PHPUnit_Framework_TestCase
{
    protected $notification;

    public function setUp()
    {
        $service = $this->getMock('\Jgut\Tify\Service\GcmService', [], [], '', false);
        $message = $this->getMock('\Jgut\Tify\Message\GcmMessage', [], [], '', false);

        $this->notification = new GcmNotification($service, $message);
    }

    /**
     * @covers \Jgut\Tify\Notification\GcmNotification::setService
     *
     * @expectedException \InvalidArgumentException
     */
    public function testServiceSet()
    {
        $service = $this->getMock('\Jgut\Tify\Service\GcmService', [], [], '', false);
        $this->notification->setService($service);
        $this->assertEquals($service, $this->notification->getService());

        $service = $this->getMock('\Jgut\Tify\Service\AbstractService', [], [], '', false);

        $this->notification->setService($service);
    }

    /**
     * @covers \Jgut\Tify\Notification\GcmNotification::setMessage
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMessageSet()
    {
        $message = $this->getMock('\Jgut\Tify\Message\GcmMessage', [], [], '', false);
        $this->notification->setMessage($message);
        $this->assertEquals($message, $this->notification->getMessage());

        $message = $this->getMock('\Jgut\Tify\Message\AbstractMessage', [], [], '', false);

        $this->notification->setMessage($message);
    }

    /**
     * @covers \Jgut\Tify\Notification\GcmNotification::addRecipient
     *
     * @expectedException \InvalidArgumentException
     */
    public function testRecipientAdd()
    {
        $recipient = $this->getMock('\Jgut\Tify\Recipient\GcmRecipient', [], [], '', false);
        $this->notification->addRecipient($recipient);
        $this->assertCount(1, $this->notification->getRecipients());

        $recipient = $this->getMock('\Jgut\Tify\Recipient\AbstractRecipient', [], [], '', false);

        $this->notification->addRecipient($recipient);
    }
}
