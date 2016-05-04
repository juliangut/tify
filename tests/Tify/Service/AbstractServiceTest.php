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
 * AbstractService tests.
 */
class AbstractServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\Service\AbstractService
     */
    protected $service;

    public function setUp()
    {
        $this->service = $this->getMockForAbstractClass(AbstractService::class, [], '', false);
    }

    public function testDefaults()
    {
        self::assertFalse($this->service->isSandbox());
    }

    public function testAccessorsMutators()
    {
        $this->service->setSandbox(true);
        self::assertTrue($this->service->isSandbox());

        $this->service->setSandbox(false);
        self::assertFalse($this->service->isSandbox());
    }
}
