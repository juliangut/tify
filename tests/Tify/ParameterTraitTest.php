<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests;

use Jgut\Tify\ParameterTrait;

/**
 * ParameterTrait tests.
 */
class ParameterTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\ParameterTrait
     */
    protected $parameterBag;

    public function setUp()
    {
        $this->parameterBag = $this->getMockForTrait(ParameterTrait::class);
    }

    public function testDefaults()
    {
        self::assertNull($this->parameterBag->getParameter('any_parameter'));
        self::assertEmpty($this->parameterBag->getParameters());
    }

    public function testAccessorsMutators()
    {
        $this->parameterBag->setParameter('first', true);
        self::assertTrue($this->parameterBag->hasParameter('first'));
        self::assertTrue($this->parameterBag->hasParameter('first'));
        self::assertCount(1, $this->parameterBag->getParameters());

        $this->parameterBag->setParameters([
            'second' => 'second',
            'third' => 'third',
        ]);
        self::assertTrue($this->parameterBag->hasParameter('second'));
        self::assertCount(2, $this->parameterBag->getParameters());
    }
}
