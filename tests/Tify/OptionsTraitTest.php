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
 * @covers \Jgut\Tify\OptionsTrait
 */
class OptionsTraitTest extends \PHPUnit_Framework_TestCase
{
    protected $bag;

    public function setUp()
    {
        $this->bag = $this->getMockForTrait('\Jgut\Tify\OptionsTrait');
    }

    /**
     * @covers \Jgut\Tify\OptionsTrait::getOption
     * @covers \Jgut\Tify\OptionsTrait::getOptions
     */
    public function testDefaults()
    {
        $this->assertNull($this->bag->getOption('any_option'));
        $this->assertEmpty($this->bag->getOptions());
    }

    /**
     * @covers \Jgut\Tify\OptionsTrait::hasOption
     * @covers \Jgut\Tify\OptionsTrait::getOption
     * @covers \Jgut\Tify\OptionsTrait::getOptions
     * @covers \Jgut\Tify\OptionsTrait::setOption
     * @covers \Jgut\Tify\OptionsTrait::setOptions
     */
    public function testAccessorsMutators()
    {
        $this->bag->setOption('first', true);
        $this->assertTrue($this->bag->hasOption('first'));
        $this->assertTrue($this->bag->getOption('first'));
        $this->assertCount(1, $this->bag->getOptions());

        $this->bag->setOptions([
            'second' => 'second',
            'third' => 'third',
        ]);
        $this->assertTrue($this->bag->hasOption('second'));
        $this->assertCount(2, $this->bag->getOptions());
    }
}
