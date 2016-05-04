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

/**
 * Notification tests.
 */
class NotificationTest extends \PHPUnit_Framework_TestCase
{
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
        $this->message = $this->getMock(Message::class, [], [], '', false);
        $recipient = $this->getMockForAbstractClass(AbstractRecipient::class, [], '', false);

        $this->notification = $this->getMockForAbstractClass(
            Notification::class,
            [$this->message, [$recipient]]
        );
    }

    public function testDefaults()
    {
        self::assertEquals($this->message, $this->notification->getMessage());
        self::assertCount(1, $this->notification->getRecipients());
        self::assertCount(0, $this->notification->getResults());
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

    public function testResults()
    {
        $result = new Result('aaa', new \DateTime('now', new \DateTimeZone('UTC')));
        $this->notification->addResult($result);
        self::assertCount(1, $this->notification->getResults());

        $this->notification->clearResults();
        self::assertCount(0, $this->notification->getResults());
    }
}
