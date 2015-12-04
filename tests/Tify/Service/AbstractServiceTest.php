<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Service;

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
     * @covers \Jgut\Tify\Service\AbstractService::isSandbox
     */
    public function testDefaults()
    {
        $this->assertFalse($this->service->isSandbox());
    }

    /**
     * @covers \Jgut\Tify\Service\AbstractService::setSandbox
     * @covers \Jgut\Tify\Service\AbstractService::isSandbox
     */
    public function testAccessorsMutators()
    {
        $this->service->setSandbox(true);
        $this->assertTrue($this->service->isSandbox());

        $this->service->setSandbox(false);
        $this->assertFalse($this->service->isSandbox());
    }
}
