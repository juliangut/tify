<?php

/*
 * Unified push notification services abstraction (http://github.com/juliangut/tify).
 *
 * @license BSD-3-Clause
 * @link https://github.com/juliangut/tify
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

namespace Jgut\Tify\Tests\Adapter;

use Jgut\Tify\Adapter\AbstractAdapter;
use Jgut\Tify\Tests\Stubs\AdapterStub;

/**
 * Abstract service adapter tests.
 */
class AbstractAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdapterStub
     */
    protected $adapter;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->adapter = new AdapterStub(['param1' => 'value1']);
    }

    /**
     * @expectedException \Jgut\Tify\Exception\AdapterException
     * @expectedExceptionMessage Invalid parameter provided
     */
    public function testUndefinedParameter()
    {
        $this->getMockForAbstractClass(AbstractAdapter::class, [['undefined' => null]]);
    }

    /**
     * @expectedException \Jgut\Tify\Exception\AdapterException
     * @expectedExceptionMessageRegExp /^Missing parameters/
     */
    public function testMissingParameter()
    {
        $this->getMockForAbstractClass(AdapterStub::class);
    }

    public function testDefaults()
    {
        self::assertFalse($this->adapter->isSandbox());
    }

    public function testAccessorsMutators()
    {
        $this->adapter->setSandbox(true);
        self::assertTrue($this->adapter->isSandbox());

        $this->adapter->setSandbox(false);
        self::assertFalse($this->adapter->isSandbox());
    }
}
