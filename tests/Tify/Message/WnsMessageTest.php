<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Message;

use Jgut\Tify\Message\WnsMessage;

/**
 * @covers \Jgut\Tify\Message\WnsMessage
 */
class WnsMessageTest extends \PHPUnit_Framework_TestCase
{
    protected $message;

    public function setUp()
    {
        $this->message = new WnsMessage();
    }

    /**
     * @covers \Jgut\Tify\Message\WnsMessage::setParameter
     *
     * @dataProvider optionsProvider
     */
    public function testDefaults($option, $value)
    {
        $this->assertTrue($this->message->hasOption($option));
        $this->assertEquals($value, $this->message->getOption($option));
    }

    public function optionsProvider()
    {
        return [
            ['title', null],
            ['body', null],
        ];
    }

    /**
     * @covers \Jgut\Tify\Message\WnsMessage::setParameter
     */
    public function testParameters()
    {
        $this->message->setParameter('param1', 'value1');
        $this->assertCount(1, $this->message->getParameters());
    }
}
