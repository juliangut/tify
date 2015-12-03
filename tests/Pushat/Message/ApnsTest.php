<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Tests\Message;

use Jgut\Pushat\Message\Apns;

/**
 * @covers \Jgut\Pushat\Message\Apns
 */
class ApnsTest extends \PHPUnit_Framework_TestCase
{
    protected $message;

    public function setUp()
    {
        $this->message = new Apns();
    }

    /**
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
     * @covers \Jgut\Pushat\Message\Apns::setParameter
     *
     * @expectedException \InvalidArgumentException
     */
    public function testParameters()
    {
        $this->message->setParameter('param1', 'value1');
        $this->assertCount(1, $this->message->getParameters());

        $this->message->setParameter('apc', 'value');
    }
}
