<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Service\Message;

use Jgut\Tify\Service\Message\GcmMessage as Message;

/**
 * @covers \Jgut\Tify\Service\Message\GcmMessage
 */
class GcmMessageTest extends \PHPUnit_Framework_TestCase
{
    protected $message;

    public function setUp()
    {
        $this->message = new Message();
    }

    /**
     * @covers \Jgut\Tify\Service\Message\GcmMessage::getNotificationPayload
     * @covers \Jgut\Tify\Service\Message\GcmMessage::addNotificationPayload
     * @covers \Jgut\Tify\Service\Message\GcmMessage::setNotificationPayload
     * @covers \Jgut\Tify\Service\Message\GcmMessage::clearNotificationPayload
     */
    public function testMutatorsAccessors()
    {
        $this->assertCount(0, $this->message->getNotificationPayload());

        $this->message->addNotificationPayload('first', 'first');
        $this->assertCount(1, $this->message->getNotificationPayload());

        $this->message->setNotificationPayload(['first' => 'first', 'second' => 'second']);
        $this->assertCount(2, $this->message->getNotificationPayload());

        $this->message->clearNotificationPayload();
        $this->assertCount(0, $this->message->getNotificationPayload());
    }

    /**
     * @covers \Jgut\Tify\Service\Message\GcmMessage::addNotificationPayload
     *
     * @expectedException \ZendService\Google\Exception\InvalidArgumentException
     */
    public function testInvalidKey()
    {
        $this->message->addNotificationPayload('   ', 'first');
    }

    /**
     * @covers \Jgut\Tify\Service\Message\GcmMessage::addNotificationPayload
     *
     * @expectedException \ZendService\Google\Exception\RuntimeException
     */
    public function testDuplicatedKey()
    {
        $this->message->addNotificationPayload('first', 'first');
        $this->message->addNotificationPayload('first', 'first');
    }

    /**
     * @covers \Jgut\Tify\Service\Message\GcmMessage::toJson
     * @covers \Jgut\Tify\Service\Message\GcmMessage::getPayload
     */
    public function testJsonResult()
    {
        $this->message->setRegistrationIds(['sdfshj']);
        $this->message->setCollapseKey('key');
        $this->message->setDelayWhileIdle(true);
        $this->message->setTimeToLive(600);
        $this->message->setRestrictedPackageName('package.name');
        $this->message->setDryRun(true);
        $this->message->setData(['key' => 'value']);
        $this->message->addNotificationPayload('first', 'first');

        $result = json_decode($this->message->toJson());
        $this->assertTrue(isset($result->notification->first));
    }
}
