<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests;

use Jgut\Tify\PayloadTrait;

/**
 * PayloadTrait tests.
 */
class PayloadTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\PayloadTrait
     */
    protected $payloadBag;

    public function setUp()
    {
        $this->payloadBag = $this->getMockForTrait(PayloadTrait::class);
    }

    public function testDefaults()
    {
        self::assertNull($this->payloadBag->getData('any_parameter'));
        self::assertEmpty($this->payloadBag->getPayload());
    }

    public function testAccessorsMutators()
    {
        $this->payloadBag->setData('first', true);
        self::assertTrue($this->payloadBag->hasData('first'));
        self::assertTrue($this->payloadBag->getData('first'));
        self::assertCount(1, $this->payloadBag->getPayload());

        $this->payloadBag->setPayload([
            'second' => 'second',
            'third' => 'third',
        ]);
        self::assertTrue($this->payloadBag->hasData('second'));
        self::assertCount(2, $this->payloadBag->getPayload());
    }
}
