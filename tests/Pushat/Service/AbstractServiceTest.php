<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Tests\Service;

use Jgut\Pushat\Service\AbstractService;

/**
 * @covers \Jgut\Pushat\Service\AbstractService
 */
class AbstractServiceTest extends \PHPUnit_Framework_TestCase
{
    protected $service;

    public function setUp()
    {
        $this->service = $this->getMockForAbstractClass('\Jgut\Pushat\Service\AbstractService');
        $this->service->expects($this->once())->method('getDefaultParameters')->with($this->returnValue([]));
    }

    /**
     * @covers \Jgut\Pushat\Service\AbstractService::getServiceKey
     * @covers \Jgut\Pushat\Service\AbstractService::__toString
     * @covers \Jgut\Pushat\Service\AbstractService::getEnvironment
     * @covers \Jgut\Pushat\Service\AbstractService::isProductionEnvironment
     * @covers \Jgut\Pushat\Service\AbstractService::isDevelopmentEnvironment
     * @covers \Jgut\Pushat\Service\AbstractService::getResponse
     */
    public function testDefaults()
    {
        $this->assertNull($this->service->getServiceKey());
        $this->assertEquals('', (string) $this->service);
        $this->assertEquals(AbstractService::ENVIRONMENT_PROD, $this->service->getEnvironment());
        $this->assertTrue($this->service->isProductionEnvironment());
        $this->assertFalse($this->service->isDevelopmentEnvironment());
        $this->assertNull($this->service->getResponse());
    }

    /**
     * @covers \Jgut\Pushat\Service\AbstractService::setEnvironment
     * @covers \Jgut\Pushat\Service\AbstractService::getEnvironment
     * @covers \Jgut\Pushat\Service\AbstractService::isProductionEnvironment
     * @covers \Jgut\Pushat\Service\AbstractService::isDevelopmentEnvironment
     */
    public function testAccessorsMutators()
    {
        $this->service->setEnvironment(AbstractService::ENVIRONMENT_DEV);
        $this->assertEquals(AbstractService::ENVIRONMENT_DEV, $this->service->getEnvironment());
        $this->assertFalse($this->service->isProductionEnvironment());
        $this->assertTrue($this->service->isDevelopmentEnvironment());
    }
}
