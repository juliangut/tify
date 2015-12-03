<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Device;

use Jgut\Tify\Device\Gcm;

/**
 * @covers \Jgut\Tify\Device\Gcm
 */
class GcmTest extends \PHPUnit_Framework_TestCase
{
    protected $device;

    public function setUp()
    {
        $this->device = new Gcm('f59c88b12035278b86f26d448835939a');
    }

    /**
     * @covers \Jgut\Tify\Device\Gcm::setToken
     *
     * @expectedException \InvalidArgumentException
     */
    public function testBadToken()
    {
        $this->device->setToken('    ');
    }
}
