<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Adapter;

use Jgut\Tify\Adapter\AbstractAdapter;
use Jgut\Tify\Tests\Mock\AdapterMock;

/**
 * Abstract adapter tests.
 */
class AbstractAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected $adapter;

    public function setUp()
    {
        $this->adapter = $this->getMockForAbstractClass(AbstractAdapter::class, [], '', false);
    }

    /**
     * @expectedException \Jgut\Tify\Exception\AdapterException
     * @expectedExceptionMessage Invalid parameter provided
     */
    public function testUndefinedParameter()
    {
        $this->getMockForAbstractClass(AbstractAdapter::class, [['undefined' => null]]);
    }

    /**
     * @expectedException \Jgut\Tify\Exception\AdapterException
     * @expectedExceptionMessageRegExp /^Missing parameters/
     */
    public function testMissingParameter()
    {
        $this->getMockForAbstractClass(AdapterMock::class);
    }

    public function testDefaults()
    {
        self::assertFalse($this->adapter->isSandbox());
    }

    public function testAccessorsMutators()
    {
        $this->adapter->setSandbox(true);
        self::assertTrue($this->adapter->isSandbox());

        $this->adapter->setSandbox(false);
        self::assertFalse($this->adapter->isSandbox());
    }
}
