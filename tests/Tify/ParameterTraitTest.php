<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Tests;

use Jgut\Tify\ParameterTrait;

/**
 * ParameterTrait tests.
 */
class ParameterTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ParameterTrait
     */
    protected $parameterBag;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->parameterBag = $this->getMockForTrait(ParameterTrait::class);
    }

    public function testDefaults()
    {
        self::assertFalse($this->parameterBag->hasParameter('any_parameter'));
        self::assertNull($this->parameterBag->getParameter('any_parameter'));
        self::assertEmpty($this->parameterBag->getParameters());
    }

    public function testGetterSetters()
    {
        $this->parameterBag->setParameter('first', true);
        self::assertTrue($this->parameterBag->hasParameter('first'));
        self::assertTrue($this->parameterBag->hasParameter('first'));
        self::assertCount(1, $this->parameterBag->getParameters());

        $this->parameterBag->setParameters([
            'second' => 'second_value',
            'third' => 'third_value',
        ]);
        self::assertTrue($this->parameterBag->hasParameter('second'));
        self::assertEquals('third_value', $this->parameterBag->getThird());
        self::assertCount(2, $this->parameterBag->getParameters());
        self::assertNull($this->parameterBag->none());
    }
}
