<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Receiver;

use Jgut\Tify\Receiver\AbstractReceiver;

/**
 * AbstractReceiver tests.
 */
class AbstractReceiverTest extends \PHPUnit_Framework_TestCase
{
    protected $receiver;

    public function setUp()
    {
        $this->receiver = $this->getMockForAbstractClass(
            AbstractReceiver::class,
            ['9a4ecb987ef59c88b12035278b86f26d44883593']
        );
    }

    public function testDefaults()
    {
        self::assertNull($this->receiver->getToken());
    }
}
