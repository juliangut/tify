<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Service\Message;

use Jgut\Tify\Service\Message\MpnsMessage as Message;

/**
 * @covers \Jgut\Tify\Service\Message\MpnsMessage
 */
class MpnsMessageTest extends \PHPUnit_Framework_TestCase
{
    protected $message;

    public function setUp()
    {
        $this->message = new Message(Message::TARGET_TOAST, Message::CLASS_IMMEDIATE_TOAST);
    }

    /**
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::getTarget
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::setTarget
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
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::getClass
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::setClass
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
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::getUuid
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::setUuid
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
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::getTitle
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::setTitle
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::getBody
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::setBody
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::getNavigateTo
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::setNavigateTo
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::getCount
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::setCount
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::getBackgroundImage
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::setBackgroundImage
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::getBackBackgroundImage
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::setBackBackgroundImage
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::getSound
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::setSound
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::isSilent
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::setSilent
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

        $this->assertNull($this->message->getCount());
        $this->message->setCount(10);
        $this->assertEquals(10, $this->message->getCount());

        $this->assertNull($this->message->getBackgroundImage());
        $this->message->setBackgroundImage('image.png');
        $this->assertEquals('image.png', $this->message->getBackgroundImage());

        $this->assertNull($this->message->getBackBackgroundImage());
        $this->message->setBackBackgroundImage('image.png');
        $this->assertEquals('image.png', $this->message->getBackBackgroundImage());

        $this->assertFalse($this->message->isSilent());
        $this->message->setSilent(true);
        $this->assertTrue($this->message->isSilent());
        $this->message->setSilent(false);
        $this->assertFalse($this->message->isSilent());

        $this->assertNull($this->message->getSound());
        $this->message->setSound('sound');
        $this->assertEquals('sound', $this->message->getSound());
    }

    /**
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::getPayload
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::addPayload
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::setPayload
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::clearPayload
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
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::addPayload
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidKey()
    {
        $this->message->addPayload('   ', 'first');
    }

    /**
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::addPayload
     *
     * @expectedException \RuntimeException
     */
    public function testDuplicatedKey()
    {
        $this->message->addPayload('first', 'first');
        $this->message->addPayload('first', 'first');
    }

    /**
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::composeToastMessage
     */
    public function testToastMessage()
    {
        $this->message->setNavigateTo('main.xaml');
        $this->message->setSound('beep.mp3');
        $this->assertEquals(1, preg_match('/<wp:Toast>/', (string) $this->message));

        $this->message->setSilent(true);
        $this->assertEquals(1, preg_match('/<wp:Toast>/', (string) $this->message));
    }

    /**
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::composeTileMessage
     */
    public function testTileMessage()
    {
        $this->message->setTarget(Message::TARGET_TILE);
        $this->message->setClass(Message::CLASS_IMMEDIATE_TILE);
        $this->message->setNavigateTo('main.xaml');
        $this->message->setCount(10);
        $this->message->setBackgroundImage('image.png');
        $this->message->setBackBackgroundImage('image.png');
        $this->message->setSound('beep.mp3');
        $this->assertEquals(1, preg_match('/<wp:Tile>/', (string) $this->message));

        $this->message->setSilent(true);
        $this->assertEquals(1, preg_match('/<wp:Tile>/', (string) $this->message));
    }

    /**
     * @covers \Jgut\Tify\Service\Message\MpnsMessage::composeRawMessage
     */
    public function testRawMessage()
    {
        $this->message->setTarget(Message::TARGET_RAW);
        $this->message->setClass(Message::CLASS_IMMEDIATE_RAW);
        $this->message->setNavigateTo('main.xaml');
        $this->message->addPayload('first', 'first');

        $this->assertEquals(1, preg_match('/<message>/', (string) $this->message));
        $this->assertEquals(1, preg_match('/<parameters>/', (string) $this->message));
    }
}
