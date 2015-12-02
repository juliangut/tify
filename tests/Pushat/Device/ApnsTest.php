<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Tests\Device;

use Jgut\Pushat\Device\Apns;

/**
 * @covers \Jgut\Pushat\Device\Apns
 */
class ApnsTest extends \PHPUnit_Framework_TestCase
{
    protected $device;

    public function setUp()
    {
        $this->device = new Apns('9a4ecb987ef59c88b12035278b86f26d448835939a4ecb987ef59c88b1203527');
    }

    /**
     * @covers \Jgut\Pushat\Device\Apns::setToken
     *
     * @expectedException \InvalidArgumentException
     */
    public function testBadToken()
    {
        $this->device->setToken('non_hex_short_token');
    }
}
