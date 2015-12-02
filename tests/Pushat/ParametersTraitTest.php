<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Tests;

use Jgut\Pushat\ParametersTrait;

/**
 * @covers \Jgut\Pushat\ParametersTrait
 */
class ParametersTraitTest extends \PHPUnit_Framework_TestCase
{
    protected $bag;

    public function setUp()
    {
        $this->bag = $this->getMockForTrait('\Jgut\Pushat\ParametersTrait');
    }

    /**
     * @covers \Jgut\Pushat\ParametersTrait::getParameter
     * @covers \Jgut\Pushat\ParametersTrait::getParameters
     */
    public function testDefaults()
    {
        $this->assertNull($this->bag->getParameter('any_parameter'));
        $this->assertEmpty($this->bag->getParameters());
    }

    /**
     * @covers \Jgut\Pushat\ParametersTrait::hasParameter
     * @covers \Jgut\Pushat\ParametersTrait::getParameter
     * @covers \Jgut\Pushat\ParametersTrait::getParameters
     * @covers \Jgut\Pushat\ParametersTrait::setParameter
     * @covers \Jgut\Pushat\ParametersTrait::setParameters
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
