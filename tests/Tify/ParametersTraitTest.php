<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests;

/**
 * @covers \Jgut\Tify\ParametersTrait
 */
class ParametersTraitTest extends \PHPUnit_Framework_TestCase
{
    protected $bag;

    public function setUp()
    {
        $this->bag = $this->getMockForTrait('\Jgut\Tify\ParametersTrait');
    }

    /**
     * @covers \Jgut\Tify\ParametersTrait::getParameter
     * @covers \Jgut\Tify\ParametersTrait::getParameters
     */
    public function testDefaults()
    {
        $this->assertNull($this->bag->getParameter('any_parameter'));
        $this->assertEmpty($this->bag->getParameters());
    }

    /**
     * @covers \Jgut\Tify\ParametersTrait::hasParameter
     * @covers \Jgut\Tify\ParametersTrait::getParameter
     * @covers \Jgut\Tify\ParametersTrait::getParameters
     * @covers \Jgut\Tify\ParametersTrait::setParameter
     * @covers \Jgut\Tify\ParametersTrait::setParameters
     */
    public function testAccessorsMutators()
    {
        $this->bag->setParameter('first', true);
        $this->assertTrue($this->bag->hasParameter('first'));
        $this->assertTrue($this->bag->getParameter('first'));
        $this->assertCount(1, $this->bag->getParameters());

        $this->bag->setParameters([
            'second' => 'second',
            'third' => 'third',
        ]);
        $this->assertTrue($this->bag->hasParameter('second'));
        $this->assertCount(2, $this->bag->getParameters());
    }
}
