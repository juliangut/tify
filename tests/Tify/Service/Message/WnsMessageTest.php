<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Service\Message;

use Jgut\Tify\Service\Message\WnsMessage as Message;

/**
 * @covers \Jgut\Tify\Service\Message\WnsMessage
 */
class WnsMessageTest extends \PHPUnit_Framework_TestCase
{
    protected $message;

    public function setUp()
    {
        $this->message = new Message(Message::TARGET_TOAST, Message::CLASS_IMMEDIATE_TOAST);
    }

    /**
     * @covers \Jgut\Tify\Service\Message\WnsMessage::getTarget
     * @covers \Jgut\Tify\Service\Message\WnsMessage::setTarget
     *
     * @expectedException \InvalidArgumentException
     */
    public function testTarget()
    {
        $this->assertEquals(Message::TARGET_TOAST, $this->message->getTarget());

        $this->message->setTarget(Message::TARGET_TILE);
        $this->assertEquals(Message::TARGET_TILE, $this->message->getTarget());

        $this->message->setTarget('unknown target');
    }

    /**
     * @covers \Jgut\Tify\Service\Message\WnsMessage::getClass
     * @covers \Jgut\Tify\Service\Message\WnsMessage::setClass
     *
     * @expectedException \InvalidArgumentException
     */
    public function testClass()
    {
        $this->assertEquals(Message::CLASS_IMMEDIATE_TOAST, $this->message->getClass());

        $this->message->setClass(Message::CLASS_IMMEDIATE_TILE);
        $this->assertEquals(Message::CLASS_IMMEDIATE_TILE, $this->message->getClass());

        $this->message->setClass('unknown class');
    }

    /**
     * @covers \Jgut\Tify\Service\Message\WnsMessage::getUuid
     * @covers \Jgut\Tify\Service\Message\WnsMessage::setUuid
     *
     * @expectedException \InvalidArgumentException
     */
    public function testUuid()
    {
        $this->assertNull($this->message->getUuid());

        $this->message->setUuid('7632bf9f-fb76-40e0-92a0-f93d23a565b0');
        $this->assertEquals('7632bf9f-fb76-40e0-92a0-f93d23a565b0', $this->message->getUuid());

        $this->message->setUuid('not_an_UUID');
    }

    /**
     * @covers \Jgut\Tify\Service\Message\WnsMessage::getTitle
     * @covers \Jgut\Tify\Service\Message\WnsMessage::setTitle
     * @covers \Jgut\Tify\Service\Message\WnsMessage::getBody
     * @covers \Jgut\Tify\Service\Message\WnsMessage::setBody
     * @covers \Jgut\Tify\Service\Message\WnsMessage::getNavigateTo
     * @covers \Jgut\Tify\Service\Message\WnsMessage::setNavigateTo
     * @covers \Jgut\Tify\Service\Message\WnsMessage::getSound
     * @covers \Jgut\Tify\Service\Message\WnsMessage::setSound
     * @covers \Jgut\Tify\Service\Message\WnsMessage::isSilent
     * @covers \Jgut\Tify\Service\Message\WnsMessage::setSilent
     */
    public function testParameters()
    {
        $this->assertNull($this->message->getTitle());
        $this->message->setTitle('title');
        $this->assertEquals('title', $this->message->getTitle());

        $this->assertNull($this->message->getBody());
        $this->message->setBody('body');
        $this->assertEquals('body', $this->message->getBody());

        $this->assertEquals('', $this->message->getNavigateTo());
        $this->message->setNavigateTo('navigateTo');
        $this->assertEquals('navigateTo', $this->message->getNavigateTo());

        $this->assertNull($this->message->getSound());
        $this->message->setSound('sound');
        $this->assertEquals('sound', $this->message->getSound());

        $this->assertFalse($this->message->isSilent());
        $this->message->setSilent(true);
        $this->assertTrue($this->message->isSilent());
        $this->message->setSilent(false);
        $this->assertFalse($this->message->isSilent());
    }

    /**
     * @covers \Jgut\Tify\Service\Message\WnsMessage::getPayload
     * @covers \Jgut\Tify\Service\Message\WnsMessage::addPayload
     * @covers \Jgut\Tify\Service\Message\WnsMessage::setPayload
     * @covers \Jgut\Tify\Service\Message\WnsMessage::clearPayload
     */
    public function testPayload()
    {
        $this->assertCount(0, $this->message->getPayload());

        $this->message->addPayload('first', 'first');
        $this->assertCount(1, $this->message->getPayload());

        $this->message->setPayload(['first' => 'first', 'second' => 'second']);
        $this->assertCount(2, $this->message->getPayload());

        $this->message->clearPayload();
        $this->assertCount(0, $this->message->getPayload());
    }

    /**
     * @covers \Jgut\Tify\Service\Message\WnsMessage::addPayload
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidKey()
    {
        $this->message->addPayload('   ', 'first');
    }

    /**
     * @covers \Jgut\Tify\Service\Message\WnsMessage::addPayload
     *
     * @expectedException \RuntimeException
     */
    public function testDuplicatedKey()
    {
        $this->message->addPayload('first', 'first');
        $this->message->addPayload('first', 'first');
    }
}
