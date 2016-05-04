<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Notification;

use Jgut\Tify\Message;
use Jgut\Tify\Notification;
use Jgut\Tify\Recipient\AbstractRecipient;
use Jgut\Tify\Result;
use Jgut\Tify\Service\AbstractService;

/**
 * Notification tests.
 */
class NotificationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\Service\AbstractService
     */
    protected $service;

    /**
     * @var \Jgut\Tify\Message
     */
    protected $message;

    /**
     * @var \Jgut\Tify\Notification
     */
    protected $notification;

    public function setUp()
    {
        $this->service = $this->getMockForAbstractClass(AbstractService::class);
        $this->message = $this->getMock(Message::class, [], [], '', false);
        $recipient = $this->getMockForAbstractClass(AbstractRecipient::class, [], '', false);

        $this->notification = $this->getMockForAbstractClass(
            Notification::class,
            [$this->service, $this->message, [$recipient]]
        );
    }

    public function testDefaults()
    {
        self::assertEquals($this->service, $this->notification->getService());
        self::assertEquals($this->message, $this->notification->getMessage());
        self::assertCount(1, $this->notification->getRecipients());
        self::assertCount(0, $this->notification->getResults());
    }

    public function testService()
    {
        $service = $this->getMockForAbstractClass(AbstractService::class);
        $this->notification->setService($service);
        self::assertEquals($service, $this->notification->getService());
    }

    public function testMessage()
    {
        $message = $this->getMock(Message::class, [], [], '', false);
        $this->notification->setMessage($message);
        self::assertEquals($message, $this->notification->getMessage());
    }

    public function testRecipients()
    {
        $recipient = $this->getMockForAbstractClass(AbstractRecipient::class, [], '', false);
        $this->notification->addRecipient($recipient);
        self::assertCount(1, $this->notification->getRecipients());

        $this->notification->clearRecipients();
        self::assertCount(0, $this->notification->getRecipients());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testStatus()
    {
        self::assertEquals(Notification::STATUS_PENDING, $this->notification->getStatus());
        self::assertFalse($this->notification->isSent());
        self::assertTrue($this->notification->isPending());

        $this->notification->setStatus(Notification::STATUS_SENT);
        self::assertEquals(Notification::STATUS_SENT, $this->notification->getStatus());
        self::assertTrue($this->notification->isSent());
        self::assertFalse($this->notification->isPending());

        $this->notification->setStatus(Notification::STATUS_PENDING);
        self::assertEquals(Notification::STATUS_PENDING, $this->notification->getStatus());
        self::assertFalse($this->notification->isSent());
        self::assertTrue($this->notification->isPending());

        $this->notification->setStatus('made_up_status');
    }

    public function testResults()
    {
        $result = new Result('aaa', new \DateTime('now', new \DateTimeZone('UTC')));
        $this->notification->addResult($result);
        self::assertCount(1, $this->notification->getResults());

        $this->notification->clearResults();
        self::assertCount(0, $this->notification->getResults());
    }
}
