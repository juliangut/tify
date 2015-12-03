<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests;

use Jgut\Tify\Manager;

/**
 * @covers \Jgut\Tify\Manager
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $manager;

    public function setUp()
    {
        $this->manager = new Manager;
    }

    /**
     * @covers \Jgut\Tify\Manager::addNotification
     * @covers \Jgut\Tify\Manager::getNotifications
     */
    public function testAccesorsMutators()
    {
        $this->assertEmpty($this->manager->getNotifications());

        $this->manager->addNotification($this->getMock(
            '\Jgut\Tify\Notification\AbstractNotification',
            [],
            [],
            '',
            false
        ));
        $this->assertCount(1, $this->manager->getNotifications());
    }

    /**
     * @covers \Jgut\Tify\Manager::push
     */
    public function testSend()
    {
        $service = $this->getMock('\Jgut\Tify\Service\Gcm', [], [], '', false);

        $message = $this->getMock('\Jgut\Tify\Message\Gcm', [], [], '', false);

        $notification = $this->getMock('\Jgut\Tify\Notification\Gcm', [], [$service, $message, []]);

        $this->manager->addNotification($notification);

        $this->assertCount(0, $this->manager->push());
    }

    /**
     * @covers \Jgut\Tify\Manager::feedback
     *
     * @expectedException \Jgut\Tify\Exception\ServiceException
     */
    public function testFeedback()
    {
        $service = $this->getMock('\Jgut\Tify\Service\Apns', [], [], '', false);
        $service->expects($this->once())->method('feedback')->will($this->returnValue([]));

        $this->assertCount(0, $this->manager->feedback($service));

        $service = $this->getMock('\Jgut\Tify\Service\Gcm', [], [], '', false);
        $this->manager->feedback($service);
    }
}
