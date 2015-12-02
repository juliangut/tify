<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Tests\Notification;

use Jgut\Pushat\Notification\Gcm;

/**
 * @covers \Jgut\Pushat\Notification\Gcm
 */
class GcmTest extends \PHPUnit_Framework_TestCase
{
    protected $notification;

    public function setUp()
    {
        $adapter = $this->getMock('\Jgut\Pushat\Adapter\Gcm', [], [], '', false);
        $message = $this->getMock('\Jgut\Pushat\Message\Gcm', [], [], '', false);

        $this->notification = new Gcm($adapter, $message);
    }

    /**
     * @covers \Jgut\Pushat\Notification\Gcm::setAdapter
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAdapterSet()
    {
        $adapter = $this->getMock('\Jgut\Pushat\Adapter\Gcm', [], [], '', false);
        $this->notification->setAdapter($adapter);
        $this->assertEquals($adapter, $this->notification->getAdapter());

        $adapter = $this->getMock('\Jgut\Pushat\Adapter\AbstractAdapter', [], [], '', false);

        $this->notification->setAdapter($adapter);
    }

    /**
     * @covers \Jgut\Pushat\Notification\Gcm::setMessage
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMessageSet()
    {
        $message = $this->getMock('\Jgut\Pushat\Message\Gcm', [], [], '', false);
        $this->notification->setMessage($message);
        $this->assertEquals($message, $this->notification->getMessage());

        $message = $this->getMock('\Jgut\Pushat\Message\AbstractMessage', [], [], '', false);

        $this->notification->setMessage($message);
    }

    /**
     * @covers \Jgut\Pushat\Notification\Gcm::addDevice
     *
     * @expectedException \InvalidArgumentException
     */
    public function testDeviceAdd()
    {
        $device = $this->getMock('\Jgut\Pushat\Device\Gcm', [], [], '', false);
        $this->notification->addDevice($device);
        $this->assertCount(1, $this->notification->getDevices());

        $device = $this->getMock('\Jgut\Pushat\Device\AbstractDevice', [], [], '', false);

        $this->notification->addDevice($device);
    }
}
