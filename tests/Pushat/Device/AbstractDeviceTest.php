<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Tests\Device;

use Jgut\Pushat\Device\AbstractDevice;

/**
 * @covers \Jgut\Pushat\Device\AbstractDevice
 */
class AbstractDeviceTest extends \PHPUnit_Framework_TestCase
{
    protected $device;

    public function setUp()
    {
        $this->device = $this->getMockForAbstractClass(
            '\Jgut\Pushat\Device\AbstractDevice',
            ['9a4ecb987ef59c88b12035278b86f26d44883593']
        );
    }

    /**
     * @covers \Jgut\Pushat\Device\AbstractDevice::getToken
     */
    public function testDefaults()
    {
        $this->assertNull($this->device->getToken());
    }
}
