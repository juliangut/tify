<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Tests\Message;

use Jgut\Pushat\Message\Gcm;

/**
 * @covers \Jgut\Pushat\Message\Gcm
 */
class GcmTest extends \PHPUnit_Framework_TestCase
{
    protected $message;

    public function setUp()
    {
        $this->message = new Gcm();
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
}
