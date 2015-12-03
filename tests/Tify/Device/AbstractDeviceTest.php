<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Device;

/**
 * @covers \Jgut\Tify\Device\AbstractDevice
 */
class AbstractDeviceTest extends \PHPUnit_Framework_TestCase
{
    protected $device;

    public function setUp()
    {
        $this->device = $this->getMockForAbstractClass(
            '\Jgut\Tify\Device\AbstractDevice',
            ['9a4ecb987ef59c88b12035278b86f26d44883593']
        );
    }

    /**
     * @covers \Jgut\Tify\Device\AbstractDevice::getToken
     */
    public function testDefaults()
    {
        $this->assertNull($this->device->getToken());
    }
}
