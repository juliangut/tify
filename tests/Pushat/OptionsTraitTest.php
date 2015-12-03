<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/pushat)
 *
 * @link https://github.com/juliangut/pushat for the canonical source repository
 *
 * @license https://github.com/juliangut/pushat/blob/master/LICENSE
 */

namespace Jgut\Pushat\Tests;

/**
 * @covers \Jgut\Pushat\OptionsTrait
 */
class OptionsTraitTest extends \PHPUnit_Framework_TestCase
{
    protected $bag;

    public function setUp()
    {
        $this->bag = $this->getMockForTrait('\Jgut\Pushat\OptionsTrait');
    }

    /**
     * @covers \Jgut\Pushat\OptionsTrait::getOption
     * @covers \Jgut\Pushat\OptionsTrait::getOptions
     */
    public function testDefaults()
    {
        $this->assertNull($this->bag->getOption('any_option'));
        $this->assertEmpty($this->bag->getOptions());
    }

    /**
     * @covers \Jgut\Pushat\OptionsTrait::hasOption
     * @covers \Jgut\Pushat\OptionsTrait::getOption
     * @covers \Jgut\Pushat\OptionsTrait::getOptions
     * @covers \Jgut\Pushat\OptionsTrait::setOption
     * @covers \Jgut\Pushat\OptionsTrait::setOptions
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
