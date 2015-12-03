<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Notification;

use Jgut\Tify\Notification\Apns;

/**
 * @covers \Jgut\Tify\Notification\Apns
 */
class ApnsTest extends \PHPUnit_Framework_TestCase
{
    protected $notification;

    public function setUp()
    {
        $service = $this->getMock('\Jgut\Tify\Service\Apns', [], [], '', false);
        $message = $this->getMock('\Jgut\Tify\Message\Apns', [], [], '', false);

        $this->notification = new Apns($service, $message);
    }

    /**
     * @covers \Jgut\Tify\Notification\Apns::setService
     *
     * @expectedException \InvalidArgumentException
     */
    public function testServiceSet()
    {
        $service = $this->getMock('\Jgut\Tify\Service\Apns', [], [], '', false);
        $this->notification->setService($service);
        $this->assertEquals($service, $this->notification->getService());

        $service = $this->getMock('\Jgut\Tify\Service\AbstractService', [], [], '', false);

        $this->notification->setService($service);
    }

    /**
     * @covers \Jgut\Tify\Notification\Apns::setMessage
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMessageSet()
    {
        $message = $this->getMock('\Jgut\Tify\Message\Apns', [], [], '', false);
        $this->notification->setMessage($message);
        $this->assertEquals($message, $this->notification->getMessage());

        $message = $this->getMock('\Jgut\Tify\Message\AbstractMessage', [], [], '', false);

        $this->notification->setMessage($message);
    }

    /**
     * @covers \Jgut\Tify\Notification\Apns::addDevice
     *
     * @expectedException \InvalidArgumentException
     */
    public function testDeviceAdd()
    {
        $device = $this->getMock('\Jgut\Tify\Device\Apns', [], [], '', false);
        $this->notification->addDevice($device);
        $this->assertCount(1, $this->notification->getDevices());

        $device = $this->getMock('\Jgut\Tify\Device\AbstractDevice', [], [], '', false);

        $this->notification->addDevice($device);
    }
}
