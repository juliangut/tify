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
use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use Jgut\Tify\Service\ApnsService;
use Jgut\Tify\Service\GcmService;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\Manager
     */
    protected $manager;

    public function setUp()
    {
        $this->manager = new Manager;
    }

    public function testAccesorsMutators()
    {
        self::assertEmpty($this->manager->getNotifications());

        $this->manager->addNotification($this->getMock(
            Notification::class,
            [],
            [],
            '',
            false
        ));
        self::assertCount(1, $this->manager->getNotifications());

        $this->manager->clearNotifications();
        self::assertCount(0, $this->manager->getNotifications());
    }

    public function testSend()
    {
        $service = $this->getMock(GcmService::class, [], [], '', false);

        $message = $this->getMock(Message::class, [], [], '', false);

        $notification = $this->getMock(Notification::class, [], [$service, $message, []]);

        $this->manager->addNotification($notification);

        self::assertCount(0, $this->manager->push());
    }

    /**
     * @expectedException \Jgut\Tify\Exception\ServiceException
     */
    public function testFeedback()
    {
        $service = $this->getMock(ApnsService::class, [], [], '', false);
        $service->expects(self::once())->method('feedback')->will(self::returnValue([]));

        self::assertCount(0, $this->manager->feedback($service));

        $service = $this->getMock('\Jgut\Tify\Service\GcmService', [], [], '', false);
        $this->manager->feedback($service);
    }
}
