<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Message;

/**
 * @covers \Jgut\Tify\Message\AbstractMessage
 */
class AbstractMessageTest extends \PHPUnit_Framework_TestCase
{
    protected $message;

    public function setUp()
    {
        $this->message = $this->getMockForAbstractClass('\Jgut\Tify\Message\AbstractMessage');
    }

    /**
     * @covers \Jgut\Tify\Message\AbstractMessage::setTitle
     * @covers \Jgut\Tify\Message\AbstractMessage::setBody
     */
    public function testMutators()
    {
        $this->message->setTitle('title');
        $this->assertEquals('title', $this->message->getOption('title'));

        $this->message->setBody('body');
        $this->assertEquals('body', $this->message->getOption('body'));
    }
}
