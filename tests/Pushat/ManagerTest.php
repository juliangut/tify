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
        $service = $this->getMock('\Jgut\Pushat\Service\Gcm', [], [], '', false);

        $message = $this->getMock('\Jgut\Pushat\Message\Gcm', [], [], '', false);

        $notification = $this->getMock('\Jgut\Pushat\Notification\Gcm', [], [$service, $message, []]);

        $this->manager->addNotification($notification);

        $this->assertCount(0, $this->manager->push());
    }

    /**
     * @covers \Jgut\Pushat\Manager::feedback
     *
     * @expectedException \Jgut\Pushat\Exception\ServiceException
     */
    public function testFeedback()
    {
        $service = $this->getMock('\Jgut\Pushat\Service\Apns', [], [], '', false);
        $service->expects($this->once())->method('feedback')->will($this->returnValue([]));

        $this->assertCount(0, $this->manager->feedback($service));

        $service = $this->getMock('\Jgut\Pushat\Service\Gcm', [], [], '', false);
        $this->manager->feedback($service);
    }
}
