<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Service;

use Jgut\Tify\Service\AbstractService;

/**
 * @covers \Jgut\Tify\Service\AbstractService
 */
class AbstractServiceTest extends \PHPUnit_Framework_TestCase
{
    protected $service;

    public function setUp()
    {
        $this->service = $this->getMockForAbstractClass('\Jgut\Tify\Service\AbstractService');
    }

    /**
     * @covers \Jgut\Tify\Service\AbstractService::getEnvironment
     * @covers \Jgut\Tify\Service\AbstractService::isProductionEnvironment
     * @covers \Jgut\Tify\Service\AbstractService::isDevelopmentEnvironment
     */
    public function testDefaults()
    {
        $this->assertEquals(AbstractService::ENVIRONMENT_PROD, $this->service->getEnvironment());
        $this->assertTrue($this->service->isProductionEnvironment());
        $this->assertFalse($this->service->isDevelopmentEnvironment());
    }

    /**
     * @covers \Jgut\Tify\Service\AbstractService::setEnvironment
     * @covers \Jgut\Tify\Service\AbstractService::getEnvironment
     * @covers \Jgut\Tify\Service\AbstractService::isProductionEnvironment
     * @covers \Jgut\Tify\Service\AbstractService::isDevelopmentEnvironment
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
