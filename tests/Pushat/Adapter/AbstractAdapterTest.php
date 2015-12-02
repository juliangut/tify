<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Tests\Adapter;

use Jgut\Pushat\Adapter\AbstractAdapter;

/**
 * @covers \Jgut\Pushat\Adapter\AbstractAdapter
 */
class AbstractAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected $adapter;

    public function setUp()
    {
        $this->adapter = $this->getMockForAbstractClass('\Jgut\Pushat\Adapter\AbstractAdapter');
        $this->adapter->expects($this->once())->method('getDefaultParameters')->with($this->returnValue([]));
    }

    /**
     * @covers \Jgut\Pushat\Adapter\AbstractAdapter::getAdapterKey
     * @covers \Jgut\Pushat\Adapter\AbstractAdapter::__toString
     * @covers \Jgut\Pushat\Adapter\AbstractAdapter::getEnvironment
     * @covers \Jgut\Pushat\Adapter\AbstractAdapter::isProductionEnvironment
     * @covers \Jgut\Pushat\Adapter\AbstractAdapter::isDevelopmentEnvironment
     * @covers \Jgut\Pushat\Adapter\AbstractAdapter::getResponse
     */
    public function testDefaults()
    {
        $this->assertNull($this->adapter->getAdapterKey());
        $this->assertEquals('', (string) $this->adapter);
        $this->assertEquals(AbstractAdapter::ENVIRONMENT_PROD, $this->adapter->getEnvironment());
        $this->assertTrue($this->adapter->isProductionEnvironment());
        $this->assertFalse($this->adapter->isDevelopmentEnvironment());
        $this->assertNull($this->adapter->getResponse());
    }

    /**
     * @covers \Jgut\Pushat\Adapter\AbstractAdapter::setEnvironment
     * @covers \Jgut\Pushat\Adapter\AbstractAdapter::getEnvironment
     * @covers \Jgut\Pushat\Adapter\AbstractAdapter::isProductionEnvironment
     * @covers \Jgut\Pushat\Adapter\AbstractAdapter::isDevelopmentEnvironment
     */
    public function testAccessorsMutators()
    {
        $this->adapter->setEnvironment(AbstractAdapter::ENVIRONMENT_DEV);
        $this->assertEquals(AbstractAdapter::ENVIRONMENT_DEV, $this->adapter->getEnvironment());
        $this->assertFalse($this->adapter->isProductionEnvironment());
        $this->assertTrue($this->adapter->isDevelopmentEnvironment());
    }
}
