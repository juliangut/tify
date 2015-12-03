<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Tests\Message;

/**
 * @covers \Jgut\Pushat\Message\AbstractMessage
 */
class AbstractMessageTest extends \PHPUnit_Framework_TestCase
{
    protected $message;

    public function setUp()
    {
        $this->message = $this->getMockForAbstractClass('\Jgut\Pushat\Message\AbstractMessage');
    }

    /**
     * @covers \Jgut\Pushat\Message\AbstractMessage::setTitle
     * @covers \Jgut\Pushat\Message\AbstractMessage::setBody
     */
    public function testMutators()
    {
        $this->message->setTitle('title');
        $this->assertEquals('title', $this->message->getOption('title'));

        $this->message->setBody('body');
        $this->assertEquals('body', $this->message->getOption('body'));
    }
}
