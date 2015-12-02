<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Tests;

use Jgut\Pushat\Manager;

/**
 * @covers \Jgut\Pushat\Manager
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $manager;

    public function setUp()
    {
        $this->manager = new Manager;
    }

    /**
     * @covers \Jgut\Pushat\Manager::addNotification
     * @covers \Jgut\Pushat\Manager::getNotifications
     */
    public function testAccesorsMutators()
    {
        $this->assertEmpty($this->manager->getNotifications());

        $this->manager->addNotification($this->getMock(
            '\Jgut\Pushat\Notification\AbstractNotification',
            [],
            [],
            '',
            false
        ));
        $this->assertCount(1, $this->manager->getNotifications());
    }

    /**
     * @covers \Jgut\Pushat\Manager::push
     */
    public function testSend()
    {
        $adapter = $this->getMock('\Jgut\Pushat\Adapter\Gcm', [], [], '', false);
        $adapter->expects($this->once())->method('send')->will($this->returnValue(true));

        $message = $this->getMock('\Jgut\Pushat\Message\Gcm', [], [], '', false);

        $notification = $this->getMock('\Jgut\Pushat\Notification\Gcm', [], [$adapter, $message, []]);

        $this->manager->addNotification($notification);

        $this->assertCount(1, $this->manager->push());
    }

    /**
     * @covers \Jgut\Pushat\Manager::feedback
     *
     * @expectedException \Jgut\Pushat\Exception\AdapterException
     */
    public function testFeedback()
    {
        $adapter = $this->getMock('\Jgut\Pushat\Adapter\Apns', [], [], '', false);
        $adapter->expects($this->once())->method('feedback')->will($this->returnValue(true));

        $this->assertTrue($this->manager->feedback($adapter));

        $adapter = $this->getMock('\Jgut\Pushat\Adapter\Gcm', [], [], '', false);
        $this->manager->feedback($adapter);
    }
}
