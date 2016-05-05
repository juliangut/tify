<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Receiver;

use Jgut\Tify\Receiver\GcmReceiver;

/**
 * Gcm receiver tests.
 */
class GcmReceiverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\Receiver\GcmReceiver
     */
    protected $receiver;

    public function setUp()
    {
        $this->receiver = new GcmReceiver('f59c88b12035278b86f26d448835939a');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadToken()
    {
        $this->receiver->setToken('    ');
    }
}
