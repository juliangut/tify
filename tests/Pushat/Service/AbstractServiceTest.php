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
    }

    /**
     * @covers \Jgut\Pushat\Service\AbstractService::getServiceKey
     * @covers \Jgut\Pushat\Service\AbstractService::__toString
     * @covers \Jgut\Pushat\Service\AbstractService::getEnvironment
     * @covers \Jgut\Pushat\Service\AbstractService::isProductionEnvironment
     * @covers \Jgut\Pushat\Service\AbstractService::isDevelopmentEnvironment
     */
    public function testDefaults()
    {
        $this->assertNotNull($this->service->getServiceKey());
        $this->assertNotEquals('', (string) $this->service);
        $this->assertEquals(AbstractService::ENVIRONMENT_PROD, $this->service->getEnvironment());
        $this->assertTrue($this->service->isProductionEnvironment());
        $this->assertFalse($this->service->isDevelopmentEnvironment());
    }

    /**
     * @covers \Jgut\Pushat\Service\AbstractService::setEnvironment
     * @covers \Jgut\Pushat\Service\AbstractService::getEnvironment
     * @covers \Jgut\Pushat\Service\AbstractService::isProductionEnvironment
     * @covers \Jgut\Pushat\Service\AbstractService::isDevelopmentEnvironment
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAccessorsMutators()
    {
        $this->service->setEnvironment(AbstractService::ENVIRONMENT_DEV);
        $this->assertEquals(AbstractService::ENVIRONMENT_DEV, $this->service->getEnvironment());
        $this->assertFalse($this->service->isProductionEnvironment());
        $this->assertTrue($this->service->isDevelopmentEnvironment());

        $this->service->setEnvironment('my_environment');
    }
}
