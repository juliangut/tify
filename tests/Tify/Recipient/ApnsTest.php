<?php
/**
 * Push notification services abstraction (http://github.com/juliangut/tify)
 *
 * @link https://github.com/juliangut/tify for the canonical source repository
 *
 * @license https://github.com/juliangut/tify/blob/master/LICENSE
 */

namespace Jgut\Tify\Tests\Recipient;

use Jgut\Tify\Recipient\ApnsRecipient;

/**
 * Apns recipient tests.
 */
class ApnsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jgut\Tify\Recipient\ApnsRecipient
     */
    protected $recipient;

    public function setUp()
    {
        $this->recipient = new ApnsRecipient('9a4ecb987ef59c88b12035278b86f26d448835939a4ecb987ef59c88b1203527');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadToken()
    {
        $this->recipient->setToken('non_hex_short_token');
    }
}
