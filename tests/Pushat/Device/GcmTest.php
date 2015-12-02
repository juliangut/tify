<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Tests\Device;

use Jgut\Pushat\Device\Gcm;

/**
 * @covers \Jgut\Pushat\Device\Gcm
 */
class GcmTest extends \PHPUnit_Framework_TestCase
{
    protected $device;

    public function setUp()
    {
        $this->device = new Gcm('f59c88b12035278b86f26d448835939a');
    }

    /**
     * @covers \Jgut\Pushat\Device\Gcm::setToken
     *
     * @expectedException \InvalidArgumentException
     */
    public function testBadToken()
    {
        $this->device->setToken('    ');
    }
}
