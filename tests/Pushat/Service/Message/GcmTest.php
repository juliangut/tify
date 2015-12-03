<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Tests\Service\Message;

use Jgut\Pushat\Service\Message\Gcm as Message;

/**
 * @covers \Jgut\Pushat\Service\Message\Gcm
 */
class Gcm extends \PHPUnit_Framework_TestCase
{
    protected $message;

    public function setUp()
    {
        $this->message = new Message();
    }

    /**
     * @covers \Jgut\Pushat\Service\Message\Gcm::getNotificationPayload
     * @covers \Jgut\Pushat\Service\Message\Gcm::addNotificationPayload
     * @covers \Jgut\Pushat\Service\Message\Gcm::setNotificationPayload
     * @covers \Jgut\Pushat\Service\Message\Gcm::clearNotificationPayload
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
     * @covers \Jgut\Pushat\Service\Message\Gcm::addNotificationPayload
     *
     * @expectedException \ZendService\Google\Exception\InvalidArgumentException
     */
    public function testInvalidKey()
    {
        $this->message->addNotificationPayload('   ', 'first');
    }

    /**
     * @covers \Jgut\Pushat\Service\Message\Gcm::addNotificationPayload
     *
     * @expectedException \ZendService\Google\Exception\RuntimeException
     */
    public function testDuplicatedKey()
    {
        $this->message->addNotificationPayload('first', 'first');
        $this->message->addNotificationPayload('first', 'first');
    }

    /**
     * @covers \Jgut\Pushat\Service\Message\Gcm::toJson
     * @covers \Jgut\Pushat\Service\Message\Gcm::getPayload
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
