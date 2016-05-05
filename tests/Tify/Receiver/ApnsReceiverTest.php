<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Receiver;

use Jgut\Tify\Receiver\ApnsReceiver;

/**
 * Apns receiver tests.
 */
class ApnsReceiverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\Receiver\ApnsReceiver
     */
    protected $receiver;

    public function setUp()
    {
        $this->receiver = new ApnsReceiver('9a4ecb987ef59c88b12035278b86f26d448835939a4ecb987ef59c88b1203527');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadToken()
    {
        $this->receiver->setToken('non_hex_short_token');
    }
}
